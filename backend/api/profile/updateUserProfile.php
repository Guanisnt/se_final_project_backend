<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Content-Type: application/json");
$input = json_decode(file_get_contents("php://input"), true);
require_once '../db_connect.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "資料庫連線失敗"]);
    exit;
} 

$uId = isset($input['uId']) ? $input['uId'] : null;
$name = isset($input['name']) ? $input['name'] : null;
$email = isset($input['email']) ? $input['email'] : null;
$phone = isset($input['phone']) ? $input['phone'] : null;
$sexual = isset($input['sexual']) ? $input['sexual'] : null;
$judgeInfo = isset($input['judgeInfo']) ? $input['judgeInfo'] : null;
$teacherInfo = isset($input['teacherInfo']) ? $input['teacherInfo'] : null;
$studentInfo = isset($input['studentInfo']) ? $input['studentInfo'] : null;
$lectureInfo = isset($input['lectureInfo']) ? $input['lectureInfo'] : null;
$attendeeInfo = isset($input['attendeeInfo']) ? $input['attendeeInfo'] : null;

// 取得 userType
$stmt = $conn->prepare("SELECT userType FROM users WHERE uId = ?");
$stmt->bind_param("s", $uId);
$stmt->execute();
$stmt->bind_result($userType);
if (!$stmt->fetch()) {
    echo json_encode(["success" => false, "error" => "使用者不存在"]);
    exit;
}
$stmt->close();

$conn->begin_transaction();

try {
    // 更新 users 表
    $updateUserFields = [];
    $updateUserParams = [];
    $updateUserTypes = "";

    if ($name !== null) {
        $updateUserFields[] = "name = ?";
        $updateUserParams[] = $name;
        $updateUserTypes .= "s";
    }
    if ($email !== null) {
        $updateUserFields[] = "email = ?";
        $updateUserParams[] = $email;
        $updateUserTypes .= "s";
    }
    if ($phone !== null) {
        $updateUserFields[] = "phone = ?";
        $updateUserParams[] = $phone;
        $updateUserTypes .= "s";
    }
    if ($sexual !== null) {
        $updateUserFields[] = "sexual = ?";
        $updateUserParams[] = $sexual;
        $updateUserTypes .= "s";
    }

    if (!empty($updateUserFields)) {
        $updateUserSql = "UPDATE users SET " . implode(", ", $updateUserFields) . " WHERE uId = ?";
        $updateUserParams[] = $uId;
        $updateUserTypes .= "s";

        $stmt = $conn->prepare($updateUserSql);
        $stmt->bind_param($updateUserTypes, ...$updateUserParams);
        if (!$stmt->execute()) {
            throw new Exception("更新 users 失敗");
        }
        $stmt->close();
    }

    // 更新對應角色表
    if ($userType === "attendee") {
        // 更新 student 表
        if ($studentInfo !== null) {
            $updateStudentFields = [];
            $updateStudentParams = [];
            $updateStudentTypes = "";

            if (isset($studentInfo['department'])) {
                $updateStudentFields[] = "department = ?";
                $updateStudentParams[] = $studentInfo['department'];
                $updateStudentTypes .= "s";
            }
            if (isset($studentInfo['grade'])) {
                $updateStudentFields[] = "grade = ?";
                $updateStudentParams[] = $studentInfo['grade'];
                $updateStudentTypes .= "s";
            }

            if (!empty($updateStudentFields)) {
                $updateStudentSql = "UPDATE student SET " . implode(", ", $updateStudentFields) . " WHERE uId = ?";
                $updateStudentParams[] = $uId;
                $updateStudentTypes .= "s";

                $stmt = $conn->prepare($updateStudentSql);
                $stmt->bind_param($updateStudentTypes, ...$updateStudentParams);
                if (!$stmt->execute()) {
                    throw new Exception("更新 student 失敗");
                }
                $stmt->close();
            }
        }

        // 更新 attendee 表
        if ($attendeeInfo !== null && isset($attendeeInfo['studentCard'])) {
            $updateAttendeeSql = "UPDATE attendee SET studentCard = ? WHERE uId = ?";
            $stmt = $conn->prepare($updateAttendeeSql);
            $stmt->bind_param("ss", $attendeeInfo['studentCard'], $uId);
            if (!$stmt->execute()) {
                throw new Exception("更新 attendee 失敗");
            }
            $stmt->close();
        }

    } else {
        // 其他 userType 更新對應表
        $updateRoleFields = [];
        $updateRoleParams = [];
        $updateRoleTypes = "";
        switch ($userType) {
            case "judge":
                if ($judgeInfo !== null && isset($judgeInfo['title'])) {
                    $updateRoleFields[] = "title = ?";
                    $updateRoleParams[] = $judgeInfo['title'];
                    $updateRoleTypes .= "s";
                }
                $table = "judge";
                break;
            case "teacher":
                if ($teacherInfo !== null) {
                    if (isset($teacherInfo['department'])) {
                        $updateRoleFields[] = "department = ?";
                        $updateRoleParams[] = $teacherInfo['department'];
                        $updateRoleTypes .= "s";
                    }
                    if (isset($teacherInfo['organization'])) {
                        $updateRoleFields[] = "organization = ?";
                        $updateRoleParams[] = $teacherInfo['organization'];
                        $updateRoleTypes .= "s";
                    }
                    if (isset($teacherInfo['title'])) {
                        $updateRoleFields[] = "title = ?";
                        $updateRoleParams[] = $teacherInfo['title'];
                        $updateRoleTypes .= "s";
                    }
                }
                $table = "teacher";
                break;
            case "student":
                if ($studentInfo !== null) {
                    if (isset($studentInfo['department'])) {
                        $updateRoleFields[] = "department = ?";
                        $updateRoleParams[] = $studentInfo['department'];
                        $updateRoleTypes .= "s";
                    }
                    if (isset($studentInfo['grade'])) {
                        $updateRoleFields[] = "grade = ?";
                        $updateRoleParams[] = $studentInfo['grade'];
                        $updateRoleTypes .= "s";
                    }
                }
                $table = "student";
                break;
            case "lecture":
                if ($lectureInfo !== null && isset($lectureInfo['title'])) {
                    $updateRoleFields[] = "title = ?";
                    $updateRoleParams[] = $lectureInfo['title'];
                    $updateRoleTypes .= "s";
                }
                $table = "lecture";
                break;
            default:
                break;
                // throw new Exception("未知的使用者類型");
        }

        if (!empty($updateRoleFields)) {
            $updateRoleSql = "UPDATE $table SET " . implode(", ", $updateRoleFields) . " WHERE uId = ?";
            $updateRoleParams[] = $uId;
            $updateRoleTypes .= "s";

            $stmt = $conn->prepare($updateRoleSql);
            $stmt->bind_param($updateRoleTypes, ...$updateRoleParams);
            if (!$stmt->execute()) {
                throw new Exception("更新 $table 失敗");
            }
            $stmt->close();
        }
    }

    $conn->commit();
    echo json_encode(["success" => true], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>

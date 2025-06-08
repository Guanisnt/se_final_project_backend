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

$uId = isset($_GET['uId']) ? $_GET['uId'] : null;
$teamId = isset($_GET['teamId']) ? $_GET['teamId'] : null;

if ($uId !== null && empty($teamId)) {
    // 如果有 uId，且沒有傳 teamId，從 attendee 表查詢所屬 teamId
    $stmt = $conn->prepare("SELECT tId FROM attendee WHERE uId = ?");
    $stmt->bind_param("s", $uId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $teamId = $row['tId'];
    } else {
        echo json_encode([
            "success" => false,
            "error" => "無法找到對應的 teamId"
        ]);
        exit;
    }

    $stmt->close();
}


// 查詢隊伍與作品資料
$sql = "
    SELECT 
        t.tId AS teamId,
        t.name AS teamName,
        t.type AS teamType,
        t.uId AS teacherId,
        t.leader AS teamLeader,
        IFNULL(t.rank, -1) AS teamRank,
        w.wId AS workId,
        w.name AS workName,
        w.abstract AS workAbstract,
        w.state AS workState,
        w.sdgs,
        IFNULL(w.introduction, '') AS workIntroduction,
        IFNULL(w.consent, '') AS workConsent,
        IFNULL(w.affidavit, '') AS workAffidavit
    FROM team t
    LEFT JOIN work w ON t.tId = w.tId
    WHERE t.tId = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $teamId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "找不到該隊伍"]);
    $stmt->close();
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$workId = $row['workId'];
$teacherId = $row['teacherId'];
$teamLeader = $row['teamLeader'];
// 查詢作品網址
$workUrls = [];
if ($workId) {
    $url_sql = "
        SELECT url
        FROM work_url
        WHERE wId = ?
    ";
    $url_stmt = $conn->prepare($url_sql);
    $url_stmt->bind_param("s", $workId);
    $url_stmt->execute();
    $url_result = $url_stmt->get_result();
    while ($url_row = $url_result->fetch_assoc()) {
        $workUrls[] = $url_row['url'];
    }
    $url_stmt->close();
}

// 查詢指導老師資料
$advisorInfo = null;
if ($teacherId) {
    $advisor_sql = "
        SELECT 
            u.name AS name,
            t.title,
            t.department,
            t.organization
        FROM users u
        JOIN teacher t ON u.uId = t.uId
        WHERE u.uId = ?
    ";
    $advisor_stmt = $conn->prepare($advisor_sql);
    $advisor_stmt->bind_param("s", $teacherId);
    $advisor_stmt->execute();
    $advisor_result = $advisor_stmt->get_result();
    if ($advisor_result->num_rows > 0) {
        $advisor_row = $advisor_result->fetch_assoc();
        $advisorInfo = [
            "name" => $advisor_row['name'],
            "teacherInfo" => [
                "title" => $advisor_row['title'],
                "department" => $advisor_row['department'],
                "organization" => $advisor_row['organization']
            ]
        ];
    }
    $advisor_stmt->close();
}

// 查詢隊伍所有組員
$member_sql = "
    SELECT 
        a.uId,
        u.name,
        s.department,
        s.grade,
        u.email,
        u.phone,
        IFNULL(a.studentCard, '') AS studentCard
    FROM attendee a
    JOIN users u ON a.uId = u.uId
    JOIN student s ON u.uId = s.uId
    WHERE a.tId = ?
";
$member_stmt = $conn->prepare($member_sql);
$member_stmt->bind_param("s", $teamId);
$member_stmt->execute();
$member_result = $member_stmt->get_result();

$teamMembers = [];
while ($member_row = $member_result->fetch_assoc()) {
    $teamMembers[] = [
        "uId" => $member_row['uId'],
        "name" => $member_row['name'],
        "email" => $member_row['email'],
        "phone" => $member_row['phone'],
        "studentInfo" => [
            "department" => $member_row['department'],
            "grade" => $member_row['grade']
        ],
        "attendeeInfo" => [
            "studentCard" => $member_row['studentCard'],
            "teamId" => $teamId,
            "workId" => $workId
        ]
    ];
}
$member_stmt->close();

// 將隊長排第一個
usort($teamMembers, function ($a, $b) use ($teamLeader) {
    if ($a['uId'] === $teamLeader) return -1;
    if ($b['uId'] === $teamLeader) return 1;
    return 0;
});

$teamInfo = [
    "workSate" => $row['workState'],
    "teamName" => $row['teamName'],
    "teamType" => $row['teamType'],
    "workName" => $row['workName'],
    "workAbstract" => $row['workAbstract'],
    "workUrls" => $workUrls,
    "sdgs" => $row['sdgs'],
    "workIntroduction" => $row['workIntroduction'],
    "workConsent" => $row['workConsent'],
    "workAffidavit" => $row['workAffidavit'],
    "rank" => (int)$row['teamRank']
];

echo json_encode([
    "success" => true,
    "teamInfo" => $teamInfo,
    "advisorInfo" => $advisorInfo,
    "totalMembers" => count($teamMembers),
    "memberInfo" => $teamMembers
]);

$stmt->close();
$conn->close();
?>
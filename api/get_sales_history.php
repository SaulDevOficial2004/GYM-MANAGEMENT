<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/api_auth.php';

requireApiRoles([
    'Administrador',
    'Dueño'
]);

$search = trim($_GET['search'] ?? '');
$period = $_GET['period'] ?? 'all';
$page = max(1,(int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

$conditions = [];
$parameters = [];
$types = '';

if($search !== ''){
    $conditions[] = "(
        tipo LIKE ?
        OR descripcion LIKE ?
        OR DATE_FORMAT(created_at,'%d/%m/%Y') LIKE ?
    )";

    $searchValue = '%' . $search . '%';

    $parameters[] = $searchValue;
    $parameters[] = $searchValue;
    $parameters[] = $searchValue;
    $types .= 'sss';
}

switch($period){
    case 'today':
        $conditions[] = 'DATE(created_at)=CURDATE()';
        break;
    case 'month':
        $conditions[] = "
            MONTH(created_at)=MONTH(CURDATE())
            AND YEAR(created_at)=YEAR(CURDATE())
        ";
        break;
    case 'year':
        $conditions[] = 'YEAR(created_at)=YEAR(CURDATE())';
        break;
    default:
        $period = 'all';
        break;
}

$where = count($conditions) > 0
    ? 'WHERE ' . implode(' AND ',$conditions)
    : '';

$countSql = "
    SELECT COUNT(*) AS total
    FROM ventas
    $where
";

$countStatement = $connect->prepare($countSql);

if(!$countStatement){
    jsonResponse([
        'success' => false,
        'message' => 'No fue posible preparar la consulta.'
    ]);
}

if($types !== ''){
    $countStatement->bind_param($types,...$parameters);
}

$countStatement->execute();

$countResult = $countStatement->get_result();
$totalRecords = (int)$countResult->fetch_assoc()['total'];
$totalPages = max(1,(int)ceil($totalRecords / $limit));

if($page > $totalPages){
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

$dataSql = "
    SELECT
        id,
        tipo,
        descripcion,
        total,
        created_at
    FROM ventas
    $where
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
";

$dataStatement = $connect->prepare($dataSql);

if(!$dataStatement){
    jsonResponse([
        'success' => false,
        'message' => 'No fue posible preparar el historial.'
    ]);
}

$dataParameters = $parameters;
$dataParameters[] = $limit;
$dataParameters[] = $offset;
$dataTypes = $types . 'ii';

$dataStatement->bind_param(
    $dataTypes,
    ...$dataParameters
);

$dataStatement->execute();

$result = $dataStatement->get_result();
$records = [];

while($row = $result->fetch_assoc()){
    $date = new DateTime($row['created_at']);

    $records[] = [
        'id' => (int)$row['id'],
        'tipo' => strtoupper(trim($row['tipo'] ?? 'OTRO')),
        'descripcion' => $row['descripcion'] ?? '',
        'total' => (float)$row['total'],
        'fecha_original' => $row['created_at'],
        'fecha' => $date->format('d/m/Y'),
        'hora' => $date->format('h:i A')
    ];
}

jsonResponse([
    'success' => true,
    'data' => $records,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total_records' => $totalRecords,
        'per_page' => $limit
    ]
], 200, JSON_UNESCAPED_UNICODE);

$countStatement->close();
$dataStatement->close();
$connect->close();
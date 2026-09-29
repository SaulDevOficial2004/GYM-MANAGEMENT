<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../includes/api_auth.php';

requireApiRoles([
    'Administrador',
    'Dueño'
]);

$search=trim($_GET['search']??'');
$status=strtoupper(trim($_GET['status']??'all'));
$page=max(1,(int)($_GET['page']??1));
$limit=20;
$offset=($page-1)*$limit;

$conditions=[];
$parameters=[];
$types='';

if($search!==''){
    $conditions[]="(
        p.nombre LIKE ?
        OR cp.folio_cliente LIKE ?
        OR cp.concepto LIKE ?
        OR u.nombre LIKE ?
        OR DATE_FORMAT(cp.fecha_subida,'%d/%m/%Y') LIKE ?
    )";

    $searchValue='%'.$search.'%';

    $parameters[]=$searchValue;
    $parameters[]=$searchValue;
    $parameters[]=$searchValue;
    $parameters[]=$searchValue;
    $parameters[]=$searchValue;
    $types.='sssss';
}

if(in_array($status,['PENDIENTE','CONFIRMADO','RECHAZADO'],true)){
    $conditions[]='cp.status=?';
    $parameters[]=$status;
    $types.='s';
}else{
    $status='all';
}

$where=count($conditions)>0
    ?'WHERE '.implode(' AND ',$conditions)
    :'';

$countSql="
    SELECT COUNT(*) AS total
    FROM comprobantes_pago cp
    INNER JOIN personas p ON p.id=cp.persona_id
    LEFT JOIN usuarios u ON u.id=cp.revisado_por
    $where
";

$countStatement=$connect->prepare($countSql);

if(!$countStatement){
    jsonResponse([
        'success'=>false,
        'message'=>'No fue posible preparar el conteo.'
    ]);
}

if($types!==''){
    $countStatement->bind_param($types,...$parameters);
}

$countStatement->execute();

$countResult=$countStatement->get_result();
$totalRecords=(int)$countResult->fetch_assoc()['total'];
$totalPages=max(1,(int)ceil($totalRecords/$limit));

if($page>$totalPages){
    $page=$totalPages;
    $offset=($page-1)*$limit;
}

$dataSql="
    SELECT
        cp.id,
        cp.persona_id,
        cp.folio_cliente,
        cp.concepto,
        cp.status,
        cp.fecha_subida,
        cp.archivo,
        cp.motivo_rechazo,
        cp.revisado_por,
        cp.fecha_revision,
        p.nombre,
        p.folio,
        p.membresia_id,
        u.nombre AS revisado_por_nombre
    FROM comprobantes_pago cp
    INNER JOIN personas p ON p.id=cp.persona_id
    LEFT JOIN usuarios u ON u.id=cp.revisado_por
    $where
    ORDER BY cp.fecha_subida DESC
    LIMIT ? OFFSET ?
";

$dataStatement=$connect->prepare($dataSql);

if(!$dataStatement){
    jsonResponse([
        'success'=>false,
        'message'=>'No fue posible preparar el historial.'
    ]);
}

$dataParameters=$parameters;
$dataParameters[]=$limit;
$dataParameters[]=$offset;
$dataTypes=$types.'ii';

$dataStatement->bind_param($dataTypes,...$dataParameters);
$dataStatement->execute();

$result=$dataStatement->get_result();
$records=[];

while($row=$result->fetch_assoc()){
    $fechaSubida=new DateTime($row['fecha_subida']);

    $fechaRevision=!empty($row['fecha_revision'])
        ?new DateTime($row['fecha_revision'])
        :null;

    $records[]=[
        'id'=>(int)$row['id'],
        'persona_id'=>(int)$row['persona_id'],
        'membresia_id'=>(int)$row['membresia_id'],
        'nombre'=>$row['nombre']??'Sin nombre',
        'folio'=>$row['folio_cliente']??'',
        'concepto'=>$row['concepto']??'Sin concepto',
        'status'=>strtoupper($row['status']??''),
        'archivo'=>$row['archivo']??'',
        'motivo'=>$row['motivo_rechazo']??'',
        'revisado_por'=>$row['revisado_por_nombre']??'Pendiente',
        'fecha_subida'=>$fechaSubida->format('d/m/Y'),
        'hora_subida'=>$fechaSubida->format('h:i A'),
        'fecha_completa'=>$fechaSubida->format('d/m/Y h:i A'),
        'fecha_revision'=>$fechaRevision
            ?$fechaRevision->format('d/m/Y h:i A')
            :'Pendiente'
    ];
}

$statsResult=$connect->query("
    SELECT
        COUNT(*) AS total,
        SUM(status='PENDIENTE') AS pending,
        SUM(status='CONFIRMADO') AS confirmed,
        SUM(status='RECHAZADO') AS rejected
    FROM comprobantes_pago
");

$stats=$statsResult
    ?$statsResult->fetch_assoc()
    :[
        'total'=>0,
        'pending'=>0,
        'confirmed'=>0,
        'rejected'=>0
    ];

jsonResponse([
    'success'=>true,
    'data'=>$records,

    'stats'=>[
        'total'=>(int)$stats['total'],
        'pending'=>(int)$stats['pending'],
        'confirmed'=>(int)$stats['confirmed'],
        'rejected'=>(int)$stats['rejected']

    ],
    'pagination'=>[
        'current_page'=>$page,
        'total_pages'=>$totalPages,
        'total_records'=>$totalRecords,
        'per_page'=>$limit
    ],



], 200, JSON_UNESCAPED_UNICODE);

$countStatement->close();
$dataStatement->close();
$connect->close();
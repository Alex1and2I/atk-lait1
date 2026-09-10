<?php
// Обработчик формы заявки. Требует PHP 7.4+ на хостинге.
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не поддерживается.'], JSON_UNESCAPED_UNICODE);
    exit;
}

function clean($value): string {
    return trim(strip_tags((string)$value));
}

$name = clean($_POST['name'] ?? '');
$company = clean($_POST['company'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$fuel = clean($_POST['fuel'] ?? '');
$volume = clean($_POST['volume'] ?? '');
$unit = clean($_POST['unit'] ?? '');
$delivery = clean($_POST['delivery'] ?? '');
$date = clean($_POST['date'] ?? '');
$address = clean($_POST['address'] ?? '');
$comment = clean($_POST['comment'] ?? '');

if ($name === '' || $company === '' || !$email || $fuel === '' || $volume === '' || $delivery === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Пожалуйста, заполните обязательные поля.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$to = 'atk-lait@mail.ru';
$subject = 'Заявка с сайта АТК ЛАЙТ';
$body = "Новая заявка на поставку топлива

"
      . "Имя: {$name}
"
      . "Компания: {$company}
"
      . "Телефон: {$phone}
"
      . "E-mail: {$email}
"
      . "Топливо: {$fuel}
"
      . "Объём: {$volume} {$unit}
"
      . "Способ поставки: {$delivery}
"
      . "Желаемая дата: {$date}
"
      . "Адрес: {$address}
"
      . "Комментарий: " . ($comment !== '' ? $comment : '—') . "
";

$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'From: ATK LAIT Website <no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '>',
    'Reply-To: ' . $email,
];

$sent = mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("
", $headers));

if (!$sent) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Почтовый сервер не принял сообщение.'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);

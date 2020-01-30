<?php
/**
 * Created by PhpStorm.
 * User: zhubanov_d
 * Date: 31.01.2019
 * Time: 18:23
 */
function teleToLog($log)
{
    $myFile = 'logs.txt';
    $fh = fopen($myFile, 'a') or die('can\'t open file');
    if ((is_array($log)) || (is_object($log))) {
        $updateArray = print_r($log, TRUE);
        fwrite($fh, $updateArray . "\n");
    } else {
        fwrite($fh, $log . "\n");
    }
    fclose($fh);
}

function requestToTelegram($data, $type = 'sendMessage')
{
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, API_URL . $type);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_exec($curl);
        curl_close($curl);
    }
}


function keyboard_commands($chat_id, $text)
{
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Порт под камеру', 'callback_data' => 'cams'],
            ],
            [
                ['text' => 'Порт под Юр.Лицо', 'callback_data' => 'urik'],
            ],
            [
                ['text' => 'Порт под Физ.Лицо/Настройка ГШ', 'callback_data' => 'ipoeFiz'],
            ],
            [
                ['text' => 'Порт под статику', 'callback_data' => 'ipn'],
            ],
            [
                ['text' => 'Порт под домофон', 'callback_data' => 'intercom'],
            ],
            [
                ['text' => 'Порт под телефонию ЮЛ', 'callback_data' => 'phone'],
            ],
            [
                ['text' => 'Кабель диагностика порта', 'callback_data' => 'cabelDiagnostic'],
            ],
            [
                ['text' => 'Просмотр ошибок за портом', 'callback_data' => 'get_crc'],
            ],
            [
                ['text' => 'Очистка сч-ка ошибок', 'callback_data' => 'clear_counters'],
            ],
        ]
    ];
    $keyboard_commands = json_encode($keyboard);

    $parameters =
        array(
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => $keyboard_commands
        );
    requestToTelegram($parameters);
}


function keyboard_confirmed($chat_id, $text)
{
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Да', 'callback_data' => 'Yes'],
                ['text' => 'Нет', 'callback_data' => 'No'],
            ],

        ]
    ];
    $keyboard_confirmed = json_encode($keyboard);

    $parameters =
        array(
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => $keyboard_confirmed
        );
    requestToTelegram($parameters);
}

function identification($command)
{
    switch ($command) {
        case 'cams':
            return "Настройка порта под камеру";
            break;

        case 'urik':
            return "Настройка порта под Юр. Лицо";
            break;

        case 'ipn':
            return "Настройка порта под статику";
            break;

        case 'intercom':
            return "Настройка порта под домофон";
            break;

        case 'phone':
            return "Настройка порта под телефонию";
            break;

        case 'cabelDiagnostic':
            return "Кабель диагностика";
            break;

        case 'clear_counters':
            return "Очистить счетчик ошибок на порту";
            break;

        case 'get_crc':
            return "Просмотр ошибок на порту";
            break;
        case 'ipoeFiz':
            return "Настройка порта под Физ Лицо";
            break;
        default:
            return "не опознанная команда";
            break;
    }
}

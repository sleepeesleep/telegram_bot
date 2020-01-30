<?php
include 'config/const.php';
include 'moduls/tamburine.php';
include 'moduls/telegram.php';
include 'moduls/memcache.php';

//принимаем запрос от бота(то что напишет в чате пользователь)
$content = file_get_contents('php://input');

//превщаем из json в массив
$update = json_decode($content, TRUE);


if (isset($update['callback_query']) == TRUE) {
    $chat_id = $update['callback_query']['from']['id'];
    $name = $update['callback_query']['from']['first_name'];
    $data_callback = $update['callback_query']['data'];
    $user_id = $update['callback_query']['from']['id'];
}
if (isset($update['message']) == TRUE) {
    $chat_id = $update['message']['chat']['id'];
    $name = $update['message']['from']['first_name'];
    $text = $update['message']['text'];
    $user_id = $update['message']['from']['id'];
    $type = $update['message']['chat']['type'];
}


$memcached = new Memcached();
$memcached->addServer('127.0.0.1', 11211);
$login = $memcached->get($chat_id . "_token");

if (isset($login['token']) == TRUE) {

    if (isset($update['callback_query']) == TRUE) {


        //FIXME тут пиздец надо по нормальному сделать
        if ($data_callback == 'cams' or $data_callback == 'urik' or $data_callback == 'ipn' or $data_callback == 'intercom' or
            $data_callback == 'phone' or $data_callback == 'cabelDiagnostic' or $data_callback == 'get_crc' or $data_callback == 'clear_counters' or
        $data_callback == 'ipoeFiz') {
            $data = array(
                'text' => "Введите Ip коммутатора и порт через пробел " . $name . " Ты выбрал команду " . $data_callback,
                'chat_id' => $chat_id
            );
            requestToTelegram($data);

            $set_command = array(
                'command' => $data_callback,
                'ip_switch' => 0,
                'port_switch' => 0
            );
            $memcached->set($chat_id . "_command", $set_command, 300);
        }

        if ($data_callback == 'Yes') {
            $mem = $memcached->get($chat_id . "_command");
            $data_tamburine = array
            (
                'ip_switch' => $mem['ip_switch'],
                'port' => $mem['port_switch']
            );
            switch ($mem['command']) {
                case 'cams':
                    $send_cams = sendCams($data_tamburine, $login['token']);
                    if ($send_cams['status'] == 'ok') {
                        $data = array(
                            'text' => "Порт под камеру настроен",
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    } elseif ($send_cams['status'] == 'error') {
                        $data = array(
                            'text' => $send_cams['message'],
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    }
                    $memcached->delete($chat_id . '_command');
                    keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
                    break;

                case 'ipn':
                    $send_ipn = sendIpn($data_tamburine, $login['token']);
                    if ($send_ipn['status'] == 'ok') {
                        $data = array(
                            'text' => "Порт под статику настроен",
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    } elseif ($send_ipn['status'] == 'error') {
                        $data = array(
                            'text' => $send_ipn['message'],
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    }
                    $memcached->delete($chat_id . '_command');
                    keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
                    break;

                case 'urik':
                    $send_urik = sendUrik($data_tamburine, $login['token']);
                    if ($send_urik['status'] == 'ok') {
                        $data = array(
                            'text' => "Порт под Юр.Лицо настроен",
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    } elseif ($send_urik['status'] == 'error') {
                        $data = array(
                            'text' => $send_urik['message'],
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    }
                    $memcached->delete($chat_id . '_command');
                    keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
                    break;

                case 'intercom':
                    $send_intercom = sendIntercom($data_tamburine, $login['token']);
                    if ($send_intercom['status'] == 'ok') {
                        $data = array(
                            'text' => "Порт под интерком настроен",
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    } elseif ($send_intercom['status'] == 'error') {
                        $data = array(
                            'text' => $send_intercom['message'],
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    }
                    $memcached->delete($chat_id . '_command');
                    keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
                    break;

                case 'phone':
                    $send_phone = sendPhone($data_tamburine, $login['token']);
                    if ($send_phone['status'] == 'ok') {
                        $data = array(
                            'text' => "Порт под телефонию настроен",
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    } elseif ($send_phone['status'] == 'error') {
                        $data = array(
                            'text' => $send_phone['message'],
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    }
                    $memcached->delete($chat_id . '_command');
                    keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
                    break;

                case 'ipoeFiz':
                    $send_ipoeFiz = sendIpoeFiz($data_tamburine, $login['token']);
                    if ($send_ipoeFiz['status'] == 'ok') {
                        $data = array(
                            'text' => "Порт под Физ.Лицо настроен",
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    } elseif ($send_ipoeFiz['status'] == 'error') {
                        $data = array(
                            'text' => $send_ipoeFiz['message'],
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    }
                    $memcached->delete($chat_id . '_command');
                    keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
                    break;

                case 'cabelDiagnostic':
                    $send_cabel_diagnostic = sendCableDiag($data_tamburine, $login['token']);
                    if ($send_cabel_diagnostic['status'] == 'ok') {
                        $data = array(
                            'chat_id' => $chat_id,
                            'text' =>   "1 пара, длина: " . $send_cabel_diagnostic['response']['1']['length'] . ", Статус: " . $send_cabel_diagnostic['response']['1']['status'] .
                                "\n\r\n\r2 пара, длина: " . $send_cabel_diagnostic['response']['2']['length'] . ", Статус: " . $send_cabel_diagnostic['response']['2']['status'] .
                                "\n\r\n\r3 пара, длина: " . $send_cabel_diagnostic['response']['3']['length'] . ", Статус: " . $send_cabel_diagnostic['response']['3']['status'] .
                                "\n\r\n\r4 пара, длина: " . $send_cabel_diagnostic['response']['4']['length'] . ", Статус: " . $send_cabel_diagnostic['response']['4']['status'],
                            'parse_mode' => 'Markdown'
                        );
                        requestToTelegram($data);

                    } elseif ($send_cabel_diagnostic['status'] == 'error') {
                        $data = array(
                            'text' => $send_cabel_diagnostic['message'],
                            'chat_id' => $chat_id);

                        requestToTelegram($data);
                    }
                    $memcached->delete($chat_id . '_command');
                    keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
                    break;

                case 'get_crc':
                    $get_crc = sendGetCrc($data_tamburine,$login['token']);
                    if ($get_crc['status'] == 'ok') {
                        $data = array(
                            'chat_id' => $chat_id,
                            'text' => "rx: " . $get_crc['response']['rx'] . " tx: " . $get_crc['response']['tx']
                        );
                        requestToTelegram($data);
                    }
                    elseif ($get_crc['status'] == 'error') {
			$data = array(
                            'text' => $get_crc['message'],
                            'chat_id' => $chat_id
                        );
                        requestToTelegram($data);
                    }
                    $memcached->delete($chat_id . '_command');
                    keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
                    break;

                case 'clear_counters':
                    $clear_counter = sendClearCounters($data_tamburine, $login['token']);
                    if ($clear_counter['status'] == 'ok') {
                        $data = array(
                            'text' => "Счетчик ошибок обнулен",
                            'chat_id' => $chat_id
                        );

                        requestToTelegram($data);
                    } elseif ($clear_counter['status'] == 'error') {
                        $data = array(
                            'text' => $clear_counter['message'],
                            'chat_id' => $chat_id
                        );

                        requestToTelegram($data);
                    }
                    $memcached->delete($chat_id . '_command');
                    keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
                    break;

            }
        }
        if ($data_callback == 'No') {
            $memcached->delete($chat_id . '_command');
            keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");

        }

    } else {
// получаем данные переписки
        $mem = $memcached->get($chat_id . "_command");
        if (isset($mem['command']) == TRUE) {
            $command_split = preg_split('/\s+/', $text);
            $ip_switch = $command_split[0];
            $port_switch = $command_split[1];

            $set_command = array
            ('command' => $mem['command'],
                'ip_switch' => $ip_switch,
                'port_switch' => $port_switch
            );
            $memcached->set($chat_id . "_command", $set_command, 300);
            $identification = identification($mem['command']);

            $message = "Вы выбрали команду $identification Ip коммутатора $ip_switch порт $port_switch все верно ?";

            keyboard_confirmed($chat_id, $message);
        }

        if ($text == '/start'){
            keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
        }


// регулярочка для выхватывания Ip из запроса типо: /ping 192.168.1.1
        if (preg_match('/\/get_crc/', $text)) {
            $command_split = preg_split('/\s+/', $text);
            // ('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/',$text,$out,PREG_PATTERN_ORDER);
            // $port = preg_match_all('/\d{1,2)^/',$text,$out,PREG_PATTERN_ORDER);
            //$outputPing = shell_exec("ping -c 3 " . $out[0][0]);
            // teleToLog($command_split);
            $ip_switch = $command_split[1];
            $port_switch = $command_split[2];

            $data = array(
                'text' => "ip Комута" . $ip_switch . " порт комута " . $port_switch,
                'chat_id' => $chat_id
            );
            requestToTelegram($data);
        }
        if ($text == '/test') {
            keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
        }
    }
} elseif (isset($login['token']) == FALSE) {
    $data_tamburine = array
    (
        'telegram_id' => $user_id
    );
    $login_tamburine = checkLoginTamburin($data_tamburine);
    if ($login_tamburine['status'] == 'ok') {
        $set_login = array(
            'token' => $login_tamburine['response']['token']
        );
        $memcached->set($chat_id . '_token', $set_login, 86400);
        keyboard_commands($chat_id, "Прошу обратить внимание, все ваши действия фиксируются. Выберите команду: ");
    } elseif ($login_tamburine['status'] == 'error') {
        $data = array
        (
            'chat_id' => $chat_id,
            'text' => $login_tamburine['message']
        );
        requestToTelegram($data);
    }
    //$data = array
    //  (
    //    'text' => "Пришлите еще сообщения что бы я вас авторизовала :)",
    //      'chat_id' => $chat_id
    //      );
//requestToTelegram($data);
}
//запись в лог
teleToLog($update);

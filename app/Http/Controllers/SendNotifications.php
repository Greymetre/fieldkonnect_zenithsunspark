<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Edujugon\PushNotification\PushNotification;
use Edujugon\PushNotification\Messages\PushMessage;
use Edujugon\PushNotification\Channels\ApnChannel;
use Edujugon\PushNotification\Channels\FcmChannel;

class SendNotifications extends Controller
{
    public static function send(array $request)
    {
        $push_message = "";
        $push_data = array("alert" => $push_message,"message" => $push_message,'image'=>'','type'=>'test_notification');

        $push = new PushNotification('fcm');

        $push->setMessage([
                'notification' => [
                        'title'=> $request['title'],
                        'body'=> $request['msg'],
                        'sound' => 'default',
                        'key' => 'data'
                        ],
                'data' => [
                        'extraPayLoad1' => 'value1',
                        'extraPayLoad2' => 'value2'
                        ]
                ])
            ->setDevicesToken([$request['fcm_token']])
            ->send();
            return ['feedback'=>$push->feedback, 'message'=>$push->message['notification']];
    }
}

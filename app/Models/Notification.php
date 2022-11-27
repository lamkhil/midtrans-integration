<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function sendFcm($order, $title, $user_id){
        Notification::create([
            'user_id'=>$user_id,
            'order_id'=>$order->id,
            'title'=>$title,
        ]);
        $data = array(
            'title'=>$title,
            'sound' => "default",
            'body'=>"Selamat, Pembayaran anda telah berhasil!",
            'color' => "#79bc64"
        );
        $fields = array(
            'to'=>User::find($user_id)->fcm,
            'notification'=>$data,
            "priority" => "high",
        );
        return Notification::sendPushNotification($fields);
    }

    private static function sendPushNotification( $fields ) {

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
           'Authorization: key=AAAAkHHiFyI:APA91bGLb8AxhoiF5PCnGGJlw35Xr4pXalDi9As-vfaj9wwahU1Nh8VwLFzHv4Cb3LdaBKNxgSBokSw4vAAWBQYEtrAA3fG4IoqoYUwnE6aSNLVuJ4kYhlhS15rWpPYM_zD_PstrScDb',
           'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
  
        // Set the url, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $url );
  
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  
        // Disabling SSL Certificate support temporarly
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
  
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
  
        // Execute post
        $result = curl_exec( $ch );
        if ( $result === false ) {
           die( 'Curl failed: ' . curl_error( $ch ) );
        }
  
        // Close connection
        curl_close( $ch );
  
        return $result;
     }
}

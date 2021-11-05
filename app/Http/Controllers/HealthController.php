<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use DB;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class HealthController extends Controller
{

    // Check DB
    public function checkDB()
    {
        
        $dbCon = '';

        try {
            $statusDB = DB::connection()->getPdo();
            $dbCon = $statusDB;
        } catch (Throwable $e) {
            // report($e);
            $dbCon = $e->getMessage();
        }

        return $dbCon;
    }   

    // Check Redis
    public function checkRedis()
    {
        
        $redisCon = '';

        try {
            $statusRedis = Redis::ping();
            $redisCon = $statusRedis;
        } catch (Throwable $e) {
            // report($e);
            $redisCon = $e->getMessage();
        }
        // $redisCon = Redis::ping();

        return $redisCon;
    } 

    // Check RabbitMQ
    public function checkRabbitMQ()
    {
        
        $rabbitMQcon = '';

        try {
            $createConRabbitMQ = new AMQPStreamConnection(
                env('RABBITMQ_HOST', 'localhost'), 
                env('RABBITMQ_PORT', '5672'),
                env('RABBITMQ_USER', null),
                env('RABBITMQ_PASS', null),
            );
            $rabbitMQcon = $createConRabbitMQ->channel();
            
        } catch (Throwable $e) {
            // report($e);
            $rabbitMQcon = $e->getMessage();
        }

        return $rabbitMQcon;
    } 

    // Return Health Check
    public function checkHealth()
    {
        $dbCon = $this->checkDB();
        $redisCon = $this->checkRedis();
        $rabbitMQcon = $this->checkRabbitMQ();

        $healthStatus = 'Fail';

        $conData = [
            'database' => 'success',
            'redis'    => 'success',
            'rabbitmq' => 'success'
        ];

        return response()->json([
            'dataConnection'      => $conData,
            'healthStatus' => 'ok'
        ], 200);
    }

}

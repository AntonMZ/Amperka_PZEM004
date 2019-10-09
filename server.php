<?php

class Electicity 
{
    public function getAdd(Request $request){
        $data = $request->get('data');

        $dataArr = json_decode($data);

        //49,3,4,20,8,162,0,0,0,0,0,0,0,0,8,67,0,0,1,244,0,0,0,0,34
        $values['sensor_device_id'] = $dataArr[0];
        $values['voltage'] = hexdec('0x' . dechex($dataArr[3]) . dechex($dataArr[4])) / 10;
        $values['current'] = hexdec('0x' . dechex($dataArr[5]) . dechex($dataArr[6])) / 1000;
        $values['power'] = hexdec('0x' . dechex($dataArr[9]) . dechex($dataArr[10])) / 10;
        $values['energy'] = hexdec('0x' . dechex($dataArr[13]) . dechex($dataArr[14]));
        $values['frequency'] = hexdec('0x' . dechex($dataArr[17]) . dechex($dataArr[18])) / 10;

        Electricity::create($values);

        $transport = new \Gelf\Transport\UdpTransport('188.64.168.16');
        $publisher = new \Gelf\Publisher();
        $publisher->addTransport($transport);

        $message = new \Gelf\Message();
        $message->setShortMessage('ElectricitySensorData')
            ->setFullMessage($dataArr)
            ->setAdditional('sensor_id', $values['sensor_device_id'])
            ->setLevel(6);

        $publisher->publish($message);

        return $this->jsonResponse('OK', 200);
    }
}
?>
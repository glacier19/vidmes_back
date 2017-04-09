<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct() {

		parent::__construct();

		$this->load->model('room_model');
		$this->load->library('upload');
    }


	public function index()
	{
		$this->load->view('welcome_message');
	}

	private function setHeaders(){		
		//header('Access-Control-Allow-Origin: *');
		//header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
//		header('Access-Control-Allow-Methods GET, POST, PUT, PATCH, DELETE, OPTIONS');
//		header('Access-Control-Allow-Headers *');
		//header('Access-Control-Allow-Headers', 'Content-Type, x-xsrf-token');
		//header('Allow', 'GET, POST, PUT, DELETE, OPTIONS');
		//header('Allow GET, POST, PUT, DELETE, OPTIONS');

		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: X-Requested-With, content-type');
	}

	public function getOptions($view = false){
		$result = $this->room_model->getConfigValues();
		//return $result;
		if($view){
			$values = array();
			foreach($result as $key => $value){
				if($value['type'] == $view){
					$values[ $value['option_key'] ] = $value['value'];
				}
			}
			//return response()->json( $values );
		}else{
			$values = $result;
		}
		//header("Content-Type: text/javascript; charset=UTF-8");
		//echo json_encode( $values );
		//exit();
		return $values;
		//return response()->json( $values );
	}

	public function getOptionByType( ){
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// 	//return redirect('home/dashboard');
		// }
		// if ($request->isMethod('post')) {
		//$type_option = $request->input('type_option');
		$this->setHeaders();
		header("Content-Type: text/javascript; charset=UTF-8");
		if( $this->input->method() == 'options' ){
			echo "";exit();
		}
		//$type_option = ( $request->input('type') )? $request->input('type') : 'room';
		//$type_option = ( $this->input->post('type') )? $this->input->post('type') : 'room';
		//$type_option = ( $this->input->input_stream('type') )? $this->input->input_stream('type') : 'room';

		$raw_input_stream = $this->input->raw_input_stream;
		$json_data = json_decode( $raw_input_stream, true );
		$type_option = ( isset($json_data['type']) )? $json_data['type'] : 'room';

		$options = $this->getOptions( $type_option );
		//return response()->json( $options );
		header("Content-Type: text/javascript; charset=UTF-8");
		echo json_encode( $options );
		exit();
		// }
	}

	public function createRoom(){
		$this->setHeaders();
		header("Content-Type: text/javascript; charset=UTF-8");
		if( $this->input->method() == 'options' ){
			echo "";exit();
		}
		srand((double) microtime() * 1000000);
		//$emailOponent = $request->input('emailOponent');
		//$emailOponent = $this->input->post('emailOponent');
		//echo "<pre>";print_r( $this->input->post() );
		//echo "<pre>";print_r( $this->input->raw_input_stream );
		//$emailOponent = $this->input->input_stream('emailOponent');
		$raw_input_stream = $this->input->raw_input_stream;
		$json_data = json_decode( $raw_input_stream, true );
		$emailOponent = $json_data['emailOponent'];
		//$emailOponent = "lajtaruk@gmail.com";
		$roomid = rand(100000000,999999999);
		//return $roomid;

		$dt = new DateTime();
		$responseData = array(
			'roomid' => $roomid,
			'create' => true, //1
		);
		$dataRoomInsert = array(
			'roomid' => $roomid,
			'oponent_email' => $emailOponent,
			'created_at' => $dt->format('Y-m-d H:i:s'),
			'updated_at' => $dt->format('Y-m-d H:i:s'),
		);
		if($this->room_model->insertRoom( $dataRoomInsert )){
			//echo $this->showRoom($roomid);
			//return response()->json( $responseData );
			header("Content-Type: text/javascript; charset=UTF-8");
			echo json_encode( $responseData );
			exit();
		}else{
			$responseData['create'] = false;
			//return response()->json( $responseData );
			header("Content-Type: text/javascript; charset=UTF-8");
			echo json_encode( $responseData );
			exit();
		}
		echo "";
		exit();
	}

	public function addMessage( ) {
		$this->setHeaders();
		header("Content-Type: text/javascript; charset=UTF-8");
		if( $this->input->method() == 'options' ){
			echo "";exit();
		}
		//$data = $request->all();
		//$data = $this->input->post();
		//$roomid = $this->input->input_stream('roomid');
		$raw_input_stream = $this->input->raw_input_stream;
		$data = json_decode( $raw_input_stream, true );
// когда летит запрос от опонента
// вместо isOffer летит creator
		$data['isOffer'] = ($data['isOffer'] == 'true') ? true : false;
		$jsonMess = $this->room_model->getMessage($data['roomid'], $data['isOffer']);
//echo "<pre>";print_r($jsonMess);exit();
		if ($data['isOffer']){
			$arrMess = (isset($jsonMess[0]['offer_message']) && !empty($jsonMess[0]['offer_message'])) ? json_decode($jsonMess[0]['offer_message'],true) : array();
		}else{
			$arrMess = (isset($jsonMess[0]['answer_message']) && !empty($jsonMess[0]['answer_message'])) ? json_decode($jsonMess[0]['answer_message'],true) : array();
		}
		$arrMess[] = $data['message'][0];
		$arrMess[] = $data['message'][1];
		$result = $this->room_model->updateMessage(json_encode($arrMess), $data['roomid'], $data['isOffer']);
		//echo ($result);
		//exit();
		
		//return response()->json( $result );
		//header("Content-Type: text/javascript; charset=UTF-8");
		echo json_encode( $result );
		exit();
		// }
	}

	public function getMessage( ){
		header("Content-Type: text/javascript; charset=UTF-8");
		$this->setHeaders();
		if( $this->input->method() == 'options' ){
			echo json_encode(array());exit();
		}
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// }
		// if ($request->isMethod('post')) {
		//$data = $request->all();
		//$data = $this->input->post();
		$raw_input_stream = $this->input->raw_input_stream;
		$data = json_decode( $raw_input_stream, true );
		$data['offer'] = ($data['offer'] == 'true') ? true : false;
		//$jsonMess = RoomModel::getMessage($data['roomid'], $data['offer']);
		$jsonMess = $this->room_model->getMessage($data['roomid'], $data['offer']);
//echo "jsonMess=";print_r($jsonMess);
//echo "<pre>";print_r(json_decode($jsonMess[0]['offer_message'], true));
//echo json_encode( $jsonMess[0]['offer_message'] );
//echo "<pre>";print_r($jsonMess);
		if (count($jsonMess) == 0){
			echo '';
			exit();
		}		
		if ($data['offer']){ // offer - true - оппонент
			$tmp_data = json_decode($jsonMess[0]['offer_message'], true);
//echo $jsonMess[0]['offer_message'];
//echo "<pre>";print_r($tmp_data);

			echo json_encode($tmp_data);
			//echo json_encode( $jsonMess[0]['offer_message'] );
		}else{// offer - false - создатель
			if( $jsonMess[0]['answer_message'] == null ){
				echo json_encode( array() );
			}else{
				$tmp_data = json_decode($jsonMess[0]['answer_message'], true);
				echo json_encode($tmp_data);
				//echo json_encode( $jsonMess[0]['answer_message'] );
			}
		}
		//echo "yes";//$jsonMess = Room::getMessage($data['roomid']);
		exit();
		// }
	}

	public function addChatMessage( ){
		$this->setHeaders();
		header("Content-Type: text/javascript; charset=UTF-8");
		if( $this->input->method() == 'options' ){
			echo "";exit();
		}
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// }
		// if ($request->isMethod('post')) {
		//$data = $request->all();
		//$data = $this->input->post();
		$raw_input_stream = $this->input->raw_input_stream;
		$data = json_decode( $raw_input_stream, true );
		$data['creator'] = ($data['creator'] == 'true') ? 1 : 0;
		//$result = RoomModel::addChatMessage($data);
		$result = $this->room_model->addChatMessage($data);
		//echo ($result);		
		//exit();
		//return response()->json( $result );
		
		echo json_encode( $result );
		exit();
		// }
	}

	public function getChatMessage( ){
		$this->setHeaders();
		header("Content-Type: text/javascript; charset=UTF-8");
		if( $this->input->method() == 'options' ){
			echo "";exit();
		}
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// }
		// if ($request->isMethod('post')) {
		//$data = $request->all();
		//$data = $this->input->post();
		$raw_input_stream = $this->input->raw_input_stream;
		$data = json_decode( $raw_input_stream, true );
		$data['creator'] = ($data['creator'] == 'true') ? 1 : 0;
//echo "lasttime=";var_dump($data['lasttime']);
		//$data['lasttime'] = ($data['lasttime'] == "") ? date('Y-m-d H:i:s') : $data['lasttime'];
		$data['lasttime'] = (mb_strlen($data['lasttime']) == 0) ? date('Y-m-d H:i:s') : $data['lasttime'];
		//$result = RoomModel::getChatMessage($data);
		$result = $this->room_model->getChatMessage($data);
		$messages = (!$result) ? '' : $result;
		$lasttime = (!$result) ? $data['lasttime'] : $result[count($result) - 1]['time'];
		$message = [
			'messages' => $result,
			'last_mess_time' => $lasttime
		];
		//echo json_encode($message);
		//exit();
		//return response()->json( $message );
		
		echo json_encode( $message );
		exit();
		// }
	}



	public function sendMail( ){
		$this->setHeaders();
		header("Content-Type: text/javascript; charset=UTF-8");
		//echo $this->input->method();exit();
		if( $this->input->method() == 'options' ){
			echo json_encode( array() );
			exit();
		}
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// }
		// if ($request->isMethod('post')) {
		//$data = $request->all();
		//$data = $this->input->post();
		$raw_input_stream = $this->input->raw_input_stream;
		$data = json_decode( $raw_input_stream, true );
		//echo "<pre>";print_r($data);exit();
		//echo "sendMail=";print_r($data);
		//exit();
		//$data['email'] = RoomModel::getEmail($data['roomid']);
		//$data['email'] = $this->room_model->getEmail($data['roomid']);
		$row_data = $this->room_model->getEmail($data['roomid']);
		$data['email'] = $row_data['oponent_email'];
		
		//$message = $this->init('email');
		$message = $this->getOptions('email');
		$message['mailtext'] = str_replace('{%room_url%}', $data['url'], $message['mailtext']);
		$from = "sergius.lajtaruk@gmail.com";
		$headers = "";
		$headers .= "Content-type: text/html; charset=UTF-8 \r\n"; 
		$headers .= "From: ".$from."\r\n"."Reply-To: ".$from."\r\n";
		//$headers .= "From: ".Request::getHost()." <admin@".Request::getHost().">\r\n";		 
		//$headers .= "Bcc: admin@".Request::getHost()."\r\n";
		//$message = 'Hello! '."\n".'You have invited to video chat, please follow this URL:'."\n".$data['url'];
//		$mailstatus = mail($data['email'], $message['mailsubject'], $message['mailtext'], $headers);
		$result_data = array(
			'mailsubject' => $message['mailsubject'],
			'mailtext' => strip_tags ( $message['mailtext'] ),

		);
		//"mailsubject":"Invite to videochat","mailtext"
		//return json_encode($mailstatus);
		//return response()->json( $mailstatus );
		
		//echo json_encode( $mailstatus );
		echo json_encode( $result_data );
		exit();
		// }
	}

	public function fileUpload() {
		$data = array();
		$raw_input_stream = $this->input->raw_input_stream;
		$data = json_decode( $raw_input_stream, true );
//echo "<pre>";print_r($data);
		$roomid = $this->input->post('roomid');
//echo $roomid;
		$data['roomid'] = Request::input('roomid');
		$data['creator'] = (Request::input('creator') == 'true') ? 1 : 0;
		$publicPath = public_path();
		$uploadPath = '/user_files';
		$roomPath = $uploadPath.'/'.$data['roomid'];
		if (Request::hasFile("0"))
		{
			$file = Request::file("0");
			if ($file->isValid())
			{
				if(!is_dir($publicPath.$roomPath))
					mkdir($publicPath.$roomPath);
				if(file_exists($publicPath.$roomPath.'/'.$file->getClientOriginalName()))
					unlink($publicPath.$roomPath.'/'.$file->getClientOriginalName());
				$file->move($publicPath.$roomPath,$file->getClientOriginalName());
				//$data['text'] = '<span data-type="file"></span>File: <a href="'.url($roomPath,$file->getClientOriginalName()).'" target="_blank">'.$file->getClientOriginalName().'</a>';
				$data['text'] = '<span data-type="file"></span>File: '.$file->getClientOriginalName().' <a class="btn btn-info" href="'.url($roomPath,$file->getClientOriginalName()).'" target="_blank">Download</a>';
				$result = Room::addChatMessage($data);
			}
		}
		echo ($result);
		exit();
	}


	public function removeRoom(){
		$this->setHeaders();
		header("Content-Type: text/javascript; charset=UTF-8");
		if( $this->input->method() == 'options' ){
			echo "";exit();
		}
		//$roomid = $request->input('roomid');
		//$roomid = $this->input->post('roomid');
		//$roomid = $this->input->input_stream('roomid');
		$raw_input_stream = $this->input->raw_input_stream;
		$json_data = json_decode( $raw_input_stream, true );
		$roomid = $json_data['roomid'];


		$delres = $this->room_model->delRoom( $roomid );
		
		header("Content-Type: text/javascript; charset=UTF-8");
		echo json_encode( array( "room_deleted" => true ) );
		exit();
/*
		$publicPath = public_path();
		$uploadPath = '/user_files/';
		$dir = $publicPath.$uploadPath.$roomid;
		if(is_dir($dir)){
			$files = array_diff(scandir($dir), array('.','..')); 
			foreach ($files as $file) {
				if (!is_dir($dir.$file)){
					unlink($dir.'/'.$file); 
				}
			}
			if (rmdir($dir) && $delres){
				//echo true;
				return response()->json( ["room_deleted" => true] );
			}else{
				return response()->json( ["room_deleted" => false] );
			}
		}else{
			if ($delres){
				return response()->json( ["room_deleted" => true] );
			}else{
				return response()->json( ["room_deleted" => false] );
			}
		}
*/
		//exit();
		// }
	}


/*
	public function getOptionByType( Request $request ){
		if ($request->isMethod('get')) {
			return response('', 200)->header('Content-Type', 'text/plain');
			//return redirect('home/dashboard');
		}
		if ($request->isMethod('post')) {
		//$type_option = $request->input('type_option');
		$type_option = ( $request->input('type') )? $request->input('type') : 'room';
		$options = $this->getOptions( $type_option );
		return response()->json( $options );
		}
	}

	public function createRoom( Request $request ){
		// if ($request->isMethod('get')) {
		// 	//return response()->json( "" );
		// 	//return response('', 200)->header('Content-Type', 'text/plain');
		// 	return response('', 200)->header('Content-Type', 'application/json');
		// }
		// if ($request->isMethod('post')) {
		//echo "123";exit();
		srand((double) microtime() * 1000000);
		//$emailCreator = Request::input('emailCreator');
		$emailOponent = $request->input('emailOponent');
		//$emailOponent = "lajtaruk@gmail.com";
		$roomid = rand(100000000,999999999);
		//return $roomid;

		$dt = new \DateTime();
		$responseData = [
			'roomid' => $roomid,
			'create' => true, //1
		];
		$dataRoomInsert = [
			'roomid' => $roomid,
			'oponent_email' => $emailOponent,
			'created_at' => $dt->format('Y-m-d H:i:s'),
			'updated_at' => $dt->format('Y-m-d H:i:s'),
		];
		if(RoomModel::insertRoom( $dataRoomInsert )){
			//echo $this->showRoom($roomid);
			return response()->json( $responseData );
		}else{
			$responseData['create'] = false;
			return response()->json( $responseData );
		}
		//exit();
		//}
	}

	public function removeRoom( Request $request ){
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// }
		// if ($request->isMethod('post')) {
		//$roomid = Request::input('roomid');
		$roomid = $request->input('roomid');
		$delres = RoomModel::delRoom( $roomid );
		$publicPath = public_path();
		$uploadPath = '/user_files/';
		$dir = $publicPath.$uploadPath.$roomid;
		if(is_dir($dir)){
			$files = array_diff(scandir($dir), array('.','..')); 
			foreach ($files as $file) {
				if (!is_dir($dir.$file)){
					unlink($dir.'/'.$file); 
				}
			}
			if (rmdir($dir) && $delres){
				//echo true;
				return response()->json( ["room_deleted" => true] );
			}else{
				return response()->json( ["room_deleted" => false] );
			}
		}else{
			if ($delres){
				return response()->json( ["room_deleted" => true] );
			}else{
				return response()->json( ["room_deleted" => false] );
			}
		}
		//exit();
		// }
	}

	public function addMessage( Request $request ){
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// }
		// if ($request->isMethod('post')) {
		$data = $request->all();
		$data['isOffer'] = ($data['isOffer'] == 'true') ? true : false;
		$jsonMess = RoomModel::getMessage($data['roomid'], $data['isOffer']);
		if ($data['isOffer']){
			$arrMess = (isset($jsonMess[0]->offer_message) && !empty($jsonMess[0]->offer_message)) ? json_decode($jsonMess[0]->offer_message,true) : array();
		}else{
			$arrMess = (isset($jsonMess[0]->answer_message) && !empty($jsonMess[0]->answer_message)) ? json_decode($jsonMess[0]->answer_message,true) : array();
		}
		$arrMess[] = $data['message'][0];
		$arrMess[] = $data['message'][1];
		$result = RoomModel::updateMessage(json_encode($arrMess), $data['roomid'], $data['isOffer']);
		//echo ($result);
		//exit();
		
		return response()->json( $result );
		// }
	}

	public function getMessage( Request $request ){
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// }
		// if ($request->isMethod('post')) {
		$data = $request->all();
		$data['offer'] = ($data['offer'] == 'true') ? true : false;
		$jsonMess = RoomModel::getMessage($data['roomid'], $data['offer']);
		if (count($jsonMess) == 0){
			echo '';
			exit();
		}
		if ($data['offer']){
			echo $jsonMess[0]->offer_message;
		}else{
			echo $jsonMess[0]->answer_message;
		}
		//echo "yes";//$jsonMess = Room::getMessage($data['roomid']);
		exit();
		// }
	}

	public function addChatMessage( Request $request ){
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// }
		// if ($request->isMethod('post')) {
		$data = $request->all();
		$data['creator'] = ($data['creator'] == 'true') ? 1 : 0;
		$result = RoomModel::addChatMessage($data);
		//echo ($result);		
		//exit();
		return response()->json( $result );
		// }
	}

	public function getChatMessage( Request $request ){
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// }
		// if ($request->isMethod('post')) {
		$data = $request->all();
		$data['creator'] = ($data['creator'] == 'true') ? 1 : 0;
		$data['lasttime'] = ($data['lasttime'] == "") ? date('Y-m-d H:i:s') : $data['lasttime'];
		$result = RoomModel::getChatMessage($data);
		$messages = (!$result) ? '' : $result;
		$lasttime = (!$result) ? $data['lasttime'] : $result[count($result) - 1]->time;
		$message = [
			'messages' => $result,
			'last_mess_time' => $lasttime
		];
		//echo json_encode($message);
		//exit();
		return response()->json( $message );
		// }
	}



	public function sendMail( Request $request ){
		// if ($request->isMethod('get')) {
		// 	return response('', 200)->header('Content-Type', 'text/plain');
		// }
		// if ($request->isMethod('post')) {
		$data = $request->all();
		//echo "sendMail=";print_r($data);
		//exit();
		$data['email'] = RoomModel::getEmail($data['roomid']);
		//$message = $this->init('email');
		$message = $this->getOptions('email');
		$message['mailtext'] = str_replace('{%room_url%}', $data['url'], $message['mailtext']);
		$from = "sergius.lajtaruk@gmail.com";
		$headers = "";
		$headers .= "Content-type: text/html; charset=UTF-8 \r\n"; 
		$headers .= "From: ".$from."\r\n"."Reply-To: ".$from."\r\n";
		//$headers .= "From: ".Request::getHost()." <admin@".Request::getHost().">\r\n";		 
		//$headers .= "Bcc: admin@".Request::getHost()."\r\n";
		//$message = 'Hello! '."\n".'You have invited to video chat, please follow this URL:'."\n".$data['url'];
		$mailstatus = mail($data['email'], $message['mailsubject'], $message['mailtext'], $headers);
		//return json_encode($mailstatus);
		return response()->json( $mailstatus );
		// }
	}*/
}

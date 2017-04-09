<?php
//error_reporting(0);
class Room_model extends CI_Model {
	
	public function getConfigValues(){		
		$sql_str = "SELECT * FROM options GROUP BY type, id";
		
		$query = $this->db->query( $sql_str );
		return ($query->num_rows() > 0)? $query->result_array() : array();
	}

	public function insertRoom( $dataRoomInsert ){
		$this->db->insert( 'rooms', $dataRoomInsert );
		return $this->db->insert_id();
	}

	public function delRoom( $roomid ){
		//$result = DB::delete('delete from rooms where roomid = ?', [$roomid]);
		//return $result;
		$this->db->where('roomid', $roomid);
		$this->db->delete('rooms');
		return true;
	}
// offer - true - когда присоединяется
	public function getMessage( $roomid, $offer ){
		$isRead = ($offer) ? ' and isReed = 1' : ''; 
		$field = ($offer) ? 'offer_message' : 'answer_message' ;
		//$result = DB::select('select '.$field.' from rooms where roomid = ?'.$isRead, [$roomid]);
		//return $result;
		$sql_str = "select ".$field." from rooms where roomid = '".$roomid."' ".$isRead;
		$query = $this->db->query( $sql_str );
		//echo $this->db->last_query();
		return ($query->num_rows() > 0)? $query->result_array() : array();
		//return ($query->num_rows() > 0)? $query->row_array() : array();
	}

	public function updateMessage($message, $roomid, $isOffer){
		$field = ($isOffer) ? 'offer_message' : 'answer_message';
		$vals = array();
		$vals[$field] = $message;
//echo "<pre>";print_r($vals);echo "</pre><br>";

		$this->db->where('roomid', $roomid);
		$this->db->update('rooms', $vals);
//echo $message;
//echo $this->db->get_compiled_update();
//		$field = ($isOffer) ? 'offer_message' : 'answer_message';
//		$result = DB::update('update rooms set '.$field.' = ? where roomid = ?', [$message, $roomid]);
		if ($isOffer){
			//$res = DB::update('update rooms set isReed = 1 where roomid = ?', [$roomid]);
			$vals2 = array();
			$vals2['isReed'] = '1';
			//$vals2[$field] = $message; // это надо или нет? добавил сам
			$this->db->where('roomid', $roomid);
			$this->db->update('rooms', $vals2);
			//echo $this->db->get_compiled_update();
		}
		//return $result;
		
/*
		$result = DB::update('update rooms set '.$field.' = ? where roomid = ?', [$message, $roomid]);
		if ($isOffer)
			$res = DB::update('update rooms set isReed = 1 where roomid = ?', [$roomid]);
		return $result;
*/
	}

	public function addChatMessage( $val ){
	
		//$data['roomid'],$data['text'],$data['creator']
//var_dump($val['text']);
//echo "<pre>";print_r($val['text']);
// text - прилетает массив с ответом от оппонента
//echo "json=".json_encode($val['text']);
$_text = "";
$_text = $val['text'];
/*if(is_array($val['text'])){
	$_text = json_encode($val['text']);
}else{
	$_text = $val['text'];
}
var_dump($_text);
*/
		$data = array(
			'room_id' => $val['roomid'],
			'message_text' => $_text,
			'is_creator' => $val['creator'],
		);
		$this->db->insert('messages', $data);
		return $this->db->insert_id();
	}

	public function getChatMessage($data) {
		//$result = DB::select('select message_text, time from messages where room_id = ? and is_creator != ? and time > ?', [$data['roomid'], $data['creator'], $data['lasttime']]);
		//return $result;
		$sql_str = "SELECT message_text, time FROM messages where room_id = ".$data['roomid']." and is_creator != ".$data['creator']." and time > '".$data['lasttime']."' ";

		$query = $this->db->query( $sql_str );
		//echo $this->db->last_query();
		//return ($query->num_rows() > 0)? $query->row_array() : array();
		return ($query->num_rows() > 0)? $query->result_array() : array();
	}

	public function getEmail($roomid) {
		//$result = DB::select('select oponent_email from rooms where roomid = ?', [$roomid]);
		//return $result[0]->oponent_email;
		$sql_str = 'select oponent_email from rooms where roomid = '.$roomid.'';
		$query = $this->db->query( $sql_str );
		return ($query->num_rows() > 0)? $query->row_array() : array();
	}
}
?>
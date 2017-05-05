<?php 

require_once('simple_html_dom.php');
$array_crawler = array(
				'NHA'=>array(
						'link_crawler'=>'http://bongdaso.com/Standing.aspx?LeagueID=1&SeasonID=90',
						'id'=>1,
						'name'=>'Ngoại Hạng Anh',
						'season_name'=>'2016-2017',
						'lichdau_t4'=>'http://bongdaso.com/LeagueSchedule.aspx?LeagueID=1&SeasonID=90&CountryRegionID=-1&Period=8',
						'lichdau_t5'=>'http://bongdaso.com/LeagueSchedule.aspx?LeagueID=1&SeasonID=90&CountryRegionID=-1&Period=9',
					),
				'TBN'=>array(
						'link_crawler'=>'http://bongdaso.com/Standing.aspx?LeagueID=4&SeasonID=94',
						'id'=>2,
						'name'=>'Tây Ban Nha',
						'season_name'=>'2016-2017',
					),
				'PHAP'=>array(
						'link_crawler'=>'http://bongdaso.com/Standing.aspx?LeagueID=6&SeasonID=92',
						'id'=>3,
						'name'=>'Pháp',
						'season_name'=>'2016-2017',
					),
					'DUC'=>array(
						'link_crawler'=>'http://bongdaso.com/Standing.aspx?LeagueID=5&SeasonID=93',
						'id'=>4,
						'name'=>'Đức',
						'season_name'=>'2016-2017',
					),
				    'ITALIA'=>array(
						'link_crawler'=>'http://bongdaso.com/Standing.aspx?LeagueID=3&SeasonID=95',
						'id'=>5,
						'name'=>'Italia',
						'season_name'=>'2016-2017',
                ),
							
	);
//crawler_bangxephang($array_crawler);
// crawler_lichthidau($array_crawler,'lichdau_t4');
 crawler_lichthidau($array_crawler);
function crawler_lichthidau($array_crawler){
	 set_time_limit( 420 );
	 	//lặp vòng lặp fix các đội
	foreach($array_crawler as $params){
		$id_giai = $params['id'];
		// Create DOM from URL or file
		$html = file_get_html($params['lichdau_t4']);
		$data = array();
		// Find all article blocks
		foreach($html->find('div.schedule_table') as $article) {
			$item = $article->find('table', 0)->outertext ;
			//parrser content dom
			$content_schedule = str_get_html($item);
			foreach($content_schedule->find('td') as $p) {
				foreach ($p->getAllAttributes() as $attr => $val) {
					$p->removeAttribute($attr);
				}    
			}
			foreach($content_schedule->find('tr') as $p) {
				foreach ($p->getAllAttributes() as $attr => $val) {
					$p->removeAttribute($attr);
				}    
			}
			foreach($content_schedule->find('span') as $p) {
				foreach ($p->getAllAttributes() as $attr => $val) {
					$p->removeAttribute($attr);
				}    
			}
			foreach($content_schedule ->find('th') as $item) {
				$content_schedule->outertext = '';
			}
			//$html->save();
			$content_schedule = $content_schedule->find('table', 0)->innertext ;
			$content_schedule = str_replace("<td></td>","",$content_schedule);
			//chu y <a de bat dau ham dau tien ( regex chu y)
			$regex_xh ="/<tr.*?>?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<\/tr>/";
		
			preg_match_all($regex_xh, $content_schedule, $matches_results);
			
			$count_item = count($matches_results[0]);
			//var_dump($count_item);die;
			for($i=0;$i<=$count_item;$i++){
				$j=1;
				$time_start= $matches_results[$j][$i];
				$time_start = preg_replace('/<[^>]*>/', '', $time_start);
				//$time_start = strtotime($time_start );
				//var_dump($time_start);die;
				//$converted = date('d M Y h.i', strtotime($time_start));
				//$reversed = date('Y-m-d H.i', strtotime($converted));
				//$converted_date = date_format('d-m-Y H:i',strtotime($time_start));
				//$time_start = new DateTime($time_start, new DateTimeZone('Asia/Bangkok'));
				//$time_start =$time_start->getTimestamp();
				//var_dump($time_start);die;
				$home_club_name = $matches_results[$j+1][$i];
				$home_club_name = preg_replace('/<[^>]*>/', '', $home_club_name);
				
				$away_club_name = $matches_results[$j+3][$i];
				$away_club_name = preg_replace('/<[^>]*>/', '', $away_club_name);
				
				$goal =  $matches_results[$j+2][$i];
				$goal_ex = explode("-", $goal);
				$home_goal=0;
				$away_goal=0;
				$is_finish=2;
				if($goal!="-"){
					$is_finish=1;
				}
				//$is_finish = 1 tran dau da ket thuc, =2 chua dien ra
				if(isset($goal_ex[0]) && !empty($goal_ex[0])){
					$home_goal=$goal_ex[0];
				}
				if(isset($goal_ex[1]) && !empty($goal_ex[1])){
					$away_goal=$goal_ex[1];
					
				}
				//check cau lac bo khong bang rong va da~ da'
				if(!empty($home_club_name) && $is_finish==1){
					$data['data_result']['data'][]=array("id"=>$i,
								"home_club_name"=>$home_club_name,
								"away_club_name"=>$away_club_name,
								"home_goal"=>$home_goal,
								"away_goal"=> $away_goal,
								"first_time_home_goal"=> 0,
								"first_time_away_goal"=> 0,
								"is_postponed"=> 2,
								"is_finish"=> $is_finish,
								"time_start"=>  $time_start
					);
					//var_dump('<pre>',$data);
				}elseif($is_finish==2){
					$data['data_fixture']['data'][]=array("id"=>$i,
								"home_club_name"=>$home_club_name,
								"away_club_name"=>$away_club_name,
								"home_goal"=>$home_goal,
								"away_goal"=> $away_goal,
								"first_time_home_goal"=> 0,
								"first_time_away_goal"=> 0,
								"is_postponed"=> 2,
								"is_finish"=> $is_finish,
								"time_start"=>  $time_start
					);
				}
				
					
			}
			//var_dump($count_item);die;
		}
		$obj_html_result = json_encode($data['data_result']);

		 $obj_html_fixture = json_encode($data['data_fixture']);
		//gen file
		$fp = fopen('/home/data/www/api.livescore/ktt/results/'.$id_giai.'_vi.js', "w+");
		if (!$fp) {

		} else {
			fwrite($fp,$obj_html_result, strlen($obj_html_result));
			fclose($fp);
		}
		//gen file
		$fp = fopen('/home/data/www/api.livescore/ktt/fixtures/'.$id_giai.'_vi.js', "w+");
		if (!$fp) {

		} else {
			fwrite($fp,$obj_html_fixture, strlen($obj_html_fixture));
			fclose($fp);
		}
	}
}
function crawler_bangxephang($array_crawler){
	
try {
	  set_time_limit( 420 );
	//lặp vòng lặp fix các đội
	foreach($array_crawler as $params){
		//var_dump('<pre>',$params['link_crawler']);die;
		// Create DOM from URL or file
		$html = file_get_html($params['link_crawler']);
		
		$season_name = $params['season_name'];
		$name_giai = $params['name'];
		$id_giai = $params['id'];
		$data = array();
		$data_list = array();
		$data_data=array();
		/*begin remove attribute*/
		foreach($html->find('td') as $p) {
			foreach ($p->getAllAttributes() as $attr => $val) {
				$p->removeAttribute($attr);
			}    
		}
		foreach($html->find('tr') as $p) {
			foreach ($p->getAllAttributes() as $attr => $val) {
				$p->removeAttribute($attr);
			}    
		}
		// Find all article blocks
		foreach($html->find('div.standing_table') as $article) {
			$item = $article->find('table', 0)->innertext ;
		// $articles[] = $item;
		}
		//var_dump($item);die;
		/*end remove*/
		/*<tr.*?>*?<td>(?<number>.*?)</td>.*?<td>(?<team>.*?)</td>.*?<td>(?<ST>.*?)</td>.*?<td>(?<T_H>.*?)</td>.*?<td>(?<H_H>.*?)</td>.*?<td>(?<B_H>.*?)</td>.*?<td>(?<Tg_H>.*?)</td>.*?<td>(?<Th_H>.*?)</td>.*?<td>(?<T_W>.*?)</td>.*?<td>(?<H_W>.*?)</td>.*?<td>(?<B_W>.*?)</td>.*?<td>(?<Tg_W>.*?)</td>.*?<td>(?<Th_W>.*?)</td>.*?<td>(?<HS>.*?)</td>.*?<td>(?<Point>.*?)</td>.*?</tr>*/
		//ST: Số trận	T: Thắng	H: Hòa	B: Bại	Tg: Số bàn thắng	Th: Số bàn thua	HS: Hiệu số	Đ: Điểm
		/*$regex_xh = "/<tr.*?>*?<td>(?<number>.*?)<\/td>.*?<td>(?<team>.*?)<\/td>.*?<td>(?<ST>.*?)<\/td>.*?<td>(?<T_H>.*?)<\/td>.*?<td>(?<H_H>.*?)<\/td>.*?<td>(?<B_H>.*?)<\/td>.*?<td>(?<Tg_H>.*?)<\/td>.*?<td>(?<Th_H>.*?)<\/td>.*?<td>(?<T_W>.*?)<\/td>.*?<td>(?<H_W>.*?)<\/td>.*?<td>(?<B_W>.*?)<\/td>.*?<td>(?<Tg_W>.*?)<\/td>.*?<td>(?<Th_W>.*?)<\/td>.*?<td>(?<HS>.*?)<\/td>.*?<td>(?<Point>.*?)<\/td>.*?<\/tr>/";*/
		$regex_xh ="/<tr.*?>*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<\/tr>/";
		
		preg_match_all($regex_xh, $item, $matches_team);
		$count_item = count($matches_team);

		// lặp 15 phần tử từ bóng đá số của bảng xếp hạng
		for($i=1;$i<=20;$i++){
			$j=1;
			$club_name =$matches_team[$j][$i];
			if(!empty($club_name)){
				$club_name =str_replace("&nbsp;","",$matches_team[$j+1][$i]);
				//loai bo cac ky tu tags
				$club_name = preg_replace('/<[^>]*>/', '', $club_name);
				//var_dump($club_name);die;
				$data[]=array('id'=>$matches_team[$j][$i],
								'football_club_name'=>$club_name,
								'total_match'=>$matches_team[$j+2][$i],
								'point'=>$matches_team[$j+14][$i],
								"total_win"=> $matches_team[$j+3][$i],
								"total_draw"=> $matches_team[$j+4][$i],
								"total_lose"=> $matches_team[$j+5][$i],
								"goal"=> $matches_team[$j+6][$i]
				);	
			}
			
		}
		$data_list['list_seasons']=array("id"=>"5337",
							"season_id"=> "54",
							"season_name"=> "$season_name",
							"table"=>$data
							);		
		$data_data['data']=array(
				"id"=> "$id_giai",
				"season_time_id"=> "54",
				"season_time_name"=> "$season_name",
				"result_round"=> "34",
				"round_type"=> "1",
				"name"=> "$name_giai",
				"list_seasons"=>$data_list['list_seasons'],
		);
		 $obj_html = json_encode($data_data);
		//var_dump('<pre>',$obj_html);
		$fp = fopen('/home/data/www/api.livescore/ktt/standings/'.$id_giai.'_vi.js', "w+");
		if (!$fp) {

		} else {
			fwrite($fp,$obj_html, strlen($obj_html));
			fclose($fp);
		}
	//	sleep(5);
	}
	}
	catch(Exception $e) {
			echo $e;
}
}

die;
?>
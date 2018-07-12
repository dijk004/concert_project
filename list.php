<?php
	session_start(); 
	include "../lib/dbconn.php"; 
	define(SCALE, 10);   //상수 정의
?>
<!DOCTYPE HTML>
<html>
<head> 
<meta charset="utf-8">
<link href="../css/common.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/board4.css" rel="stylesheet" type="text/css" media="all">
</head>
<?php
	
	//테이블 값.
	$table = "free";
	$ripple = "free_ripple";
	
	//free 테이블 생성.
	
	$flag="NO";
	$sql = "show tables from classDB";
	$result = mysqli_query($con, $sql) or die("실패원인:".mysqli_error($con));
	while($row=mysqli_fetch_row($result)){
	    if($row[0]===$table){
	        $flag ="OK";
	        break;
	    }
	}
	if($flag !=="OK"){
	    $sql= "create table $table (
                num int not null auto_increment,
                id char(15) not null,
                name  char(10) not null,
                nick  char(10) not null,
                subject char(100) not null,
                content text not null,
                regist_day char(20),
                hit int,
                is_html char(1),
                file_name_0 char(40),
                file_name_1 char(40),
                file_name_2 char(40),
                file_name_3 char(40),
                file_name_4 char(40),
                file_copied_0 char(30),
                file_copied_1 char(30),
                file_copied_2 char(30),
                file_copied_3 char(30),
                file_copied_4 char(30), 
                primary key(num)
               )";
	 
	    if(mysqli_query($con,$sql)){
	        echo "<script>alert('{$table} 테이블이 생성되었습니다.')</script>";
	    }else{
	        echo "실패원인:".mysqli_query($con);
	    }
	}
	
	//free_ripple 테이블 생성
	//parent 레코드 : free 테이블의 num을 저장시키는 공간.
	$flag="NO";
	$sql = "show tables from classDB";
	$result = mysqli_query($con, $sql) or die("실패원인:".mysqli_error($con));
	while($row=mysqli_fetch_row($result)){
	    if($row[0]===$ripple){
	        $flag ="OK";
	        break;
	    }
	}
	if($flag !=="OK"){
	    $sql= "create table $ripple (
                num int not null auto_increment,
                parent int not null,
                id char(15) not null,
                name  char(10) not null,
                nick  char(10) not null,
                content text not null,
                regist_day char(20),
                primary key(num)
               )";
	    if(mysqli_query($con,$sql)){
	        echo "<script>alert('{$ripple} 테이블이 생성되었습니다.')</script>";
	    }else{
	        echo "실패원인:".mysqli_query($con);
	    }
	}
	

	//검색 php
	if (!empty($_GET["mode"])) {
	    $mode = $_GET['mode'];
	}
	if(!empty($_POST["search"])){
	    $search =$_POST["search"];
	}
	if(!empty($_POST["find"])){
	    $find =$_POST["find"];
	}
	
	if(!empty($_GET["search"])){
	    $search =$_GET["search"];
	}
    if ($mode=="search"){
        
       $search=trim($search);//공백 제거
        
		if(!$search){
			echo("
				<script>
				 window.alert('검색할 단어를 입력해 주세요!');
			     location.href = 'list.php';
				</script>
			");
			exit;
		}
		
		$sql = "select * from $table where $find like '%$search%' order by num desc";//앞뒤 상관없이 search의 값이 $find에 있을 시  num 내림차순으로 보여준다.
	}
	else{
		$sql = "select * from $table order by num desc";
	}

	//페이지 php
	
	$result = mysqli_query($con,$sql);
	$total_record = mysqli_num_rows($result); // 전체 글 수

	// 전체 페이지 수($total_page) 계산 
	if ($total_record % SCALE == 0){
	    $total_page = floor($total_record/SCALE);
	}else{
	    $total_page = floor($total_record/SCALE) + 1;
	}
	//현재 페이지
	if(empty($_GET['page'])){
		 $page = 1;
	}else{
		 $page = $_GET['page'];
	}
	// 표시할 페이지($page)에 따라 $start 계산  
	$start = ($page - 1) * SCALE;
	//화면에 보일 글들의 번호.
	$number = $total_record - $start;
?>
<body>
<div id="wrap">
  <div id="header">
    <?php include "../lib/top_login2.php"; ?>
  </div>  <!-- end of header -->
  <div id="menu">
	<?php include "../lib/top_menu2.php"; ?>
  </div>  <!-- end of menu --> 

  <div id="content">
	<div id="col1">
		<div id="left_menu">
<?php
			include "../lib/left_menu.php";
?>
		</div>
	</div>

	<div id="col2">        
		<div id="title">
			<img src="../img/title_free.gif">
		</div>
		
		<form  name="board_form" method="post" action="list.php?table=<?=$table?>&mode=search"> 
		<div id="list_search">
			<div id="list_search1">▷ 총 <?= $total_record ?> 개의 게시물이 있습니다.  </div>
			<div id="list_search2"><img src="../img/select_search.gif"></div>
			<div id="list_search3">
				<select name="find">
                    <option value='subject'>제목</option>
                    <option value='content'>내용</option>
                    <option value='nick'>별명</option>
                    <option value='name'>이름</option>
				</select></div>
			<div id="list_search4"><input type="text" name="search"></div>
			<div id="list_search5"><input type="image" src="../img/list_search_button.gif"></div>
		</div>
		</form><!--end of 검색 form  -->
		<div class="clear"></div>

		<div id="list_top_title">
			<ul>
				<li id="list_title1"><img src="../img/list_title1.gif"></li>
				<li id="list_title2"><img src="../img/list_title2.gif"></li>
				<li id="list_title3"><img src="../img/list_title3.gif"></li>
				<li id="list_title4"><img src="../img/list_title4.gif"></li>
				<li id="list_title5"><img src="../img/list_title5.gif"></li>
			</ul>		
		</div>

		<div id="list_content">
<?php
    //scale만큼 반복 한다가 남은 레코드가 있을 시 남은 레코드만큼 반복.
    for ($i=$start; $i<$start+SCALE && $i < $total_record; $i++){
      mysqli_data_seek($result, $i);     //내림차순 , i 포인터 이동        
      $row = mysqli_fetch_array($result); //필드 명으로 각 레코드에 저장된 값을 가져온다.	      
      
	  $item_num     = $row[num];//게시판 고유번호
	  $item_id      = $row[id];//글쓴 사람의 아이디.
	  $item_name    = $row[name];// 이름
  	  $item_nick    = $row[nick];// 닉네임
	  $item_hit     = $row[hit];//조회수
      $item_date    = $row[regist_day];//날짜.
	  $item_date = substr($item_date, 0, 10);//1번째 인자의 길이를 0~10까지 자르겠다.  
	  $item_subject = str_replace(" ", "&nbsp;", $row[subject]);//제목에 공백을 $nbsp;로 바꾸겠다.

	  //리플테이블에서 프리테이블의 num을 참조하는 번호를가지고 있는 모든 레코드를 보여달라. 
	  $sql = "select * from $ripple where parent=$item_num";
	  $result2 = mysqli_query($con,$sql);
	  $num_ripple = mysqli_num_rows($result2);//레코드 개수를 가지고 온다.  

?>
			<div id="list_item">
				<div id="list_item1"><?= $number ?></div>
				<div id="list_item2"><a href="view.php?table=<?=$table?>&num=<?=$item_num?>&page=<?=$page?>"><?= $item_subject ?></a>
<?php
        //리플이 있을 시.
        if ($num_ripple){
            echo " [$num_ripple]";
        }
?>
				</div>
				<div id="list_item3"><?= $item_nick ?></div>
				<div id="list_item4"><?= $item_date ?></div>
				<div id="list_item5"><?= $item_hit ?></div>
			</div>
<?php
   	   $number--;
   }
?>
			<div id="page_button">
				<div id="page_num">
				
<?php 
		//페이지가 1이 아닐 시.
		if($page != 1){
		    $page_before = $page-1;
			echo"<a href='list.php?page=$page_back'>◀ 이전 &nbsp;&nbsp;&nbsp;&nbsp</a>";
			    
		}else{
			echo"◀ 이전 &nbsp;&nbsp;&nbsp;&nbsp";
		}
			

   // 게시판 목록 하단에 페이지 링크 번호 출력
   for ($i=1; $i<=$total_page; $i++){
       // 현재 페이지 번호 링크 안함
		if ($page == $i){
		    
			echo "<b> $i </b>";
		}
		else{ 
			echo "<a href='list.php?page=$i'> $i </a>";
		}
		
   }
   if($page !=$total_page){
       $page_after = $page+1;
       echo"<a href='list.php?page=$page_daum'>&nbsp;&nbsp;&nbsp;&nbsp;다음 ▶</a>";
   }else{
       echo"&nbsp;&nbsp;&nbsp;&nbsp;다음 ▶";
   }
   ?>
   </div>
				<div id="button">
					<a href="list.php?table=<?=$table?>&page=<?=$page?>"><img src="../img/list.png"></a>&nbsp;
<?php 
	if($userid){
?>
		<a href="write_form.php?table=<?=$table?>&page=<?=$page?>"><img src="../img/write.png"></a>
<?php
	}
?>
				</div>
			</div> <!-- end of page_button -->		
        </div> <!-- end of list content -->
		<div class="clear"></div>

	</div> <!-- end of col2 -->
  </div> <!-- end of content -->
</div> <!-- end of wrap -->

</body>
</html>
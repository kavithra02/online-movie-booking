<?php
include('database_connection.php');

if(isset($_POST["action"]))
{
    $query = "SELECT * FROM add_movie WHERE status = '1'";

    // Checkbox Filters
    if(isset($_POST["category"]) && !empty($_POST["category"]))
    {
        $category_filter = implode("','", $_POST["category"]);
        $query .= " AND category IN('".$category_filter."')";
    }
    if(isset($_POST["language"]) && !empty($_POST["language"]))
    {
        $language_filter = implode("','", $_POST["language"]);
        $query .= " AND language IN('".$language_filter."')";
    }

    // Advanced Keywords
    if(isset($_POST["advanced"]) && $_POST["advanced"] == "yes" && !empty(trim($_POST["keywords"])))
    {
        $keywords = explode(",", $_POST["keywords"]);
        $keyword_conditions = [];
        foreach($keywords as $word)
        {
            $word = trim($word);
            if($word != "")
            {
                $keyword_conditions[] = "(movie_name LIKE '%".$word."%' OR directer LIKE '%".$word."%' OR category LIKE '%".$word."%' OR language LIKE '%".$word."%')";
            }
        }
        if(!empty($keyword_conditions))
        {
            $query .= " AND (".implode(" OR ", $keyword_conditions).")";
        }
    }

    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $total_row = $statement->rowCount();
    $output = '';

    if($total_row > 0)
    {
        foreach($result as $row)
        {
            $btn_text = ($row['action'] == "running") ? "Book Now" : "Upcoming";

            $output .= '
            <div class="col-lg-4 col-md-5 col-sm-6">
                <div style="border:1px solid #ccc; border-radius:5px; padding:16px; margin-bottom:1px; height:450px;">
                    <img src="admin/image/'. $row['image'] .'" alt="" class="resize" style="height:200px;" >
                    <p align="center"><strong><h4>'. $row['movie_name'] .'</h4></strong></p>
                    Directer : '. $row['directer'] .' <br />
                    Category : '. $row['category'] .'<br />
                    Language : '. $row['language'] .'
                </div>
                <a href="movie_details.php?pass='.$row['id'].'" class="btn btn-primary" style="margin-left: 40px;margin-top: -80px;">'.$btn_text.'</a>
            </div>
            ';
        }
    }
    else
    {
        $output = '<h3>No Data Found</h3>';
    }

    echo $output;
}
?>

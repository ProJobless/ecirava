<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Points extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // Adds $points to the user's account. If the third variable is set to 'true' adds it to their score too
    // Function returns on negative or 0 points
    // If $user_id is not set, tries to use the logged in user's ID
    function add_points($points = 0, $add_to_score = FALSE, $user_id = NULL)
    {
        if($points <= 0) { return; }
        if($user_id == NULL)
        {
            if(isset($this->data['user_id']))
            {
                $user_id = $this->data['user_id'];
            }
            else { return false; }
        }

        $score_sql = "";
        if($add_to_score)
        {
            $score_sql = "`score`=score+$points,";
        }

        $query = "UPDATE `users` SET $score_sql `points`=points+$points WHERE `id`=$user_id";
        $this->db->query($query);

        return true;
    }

    // Subtracts $points from the user's account. If the third variable is set to 'true' subtracts it from their score too
    // Function returns on negative or 0 points. Scores cannot go below 0
    // If $user_id is not set, tries to use the logged in user's ID
    function remove_points($points = 0, $remove_from_score = FALSE, $user_id = NULL)
    {
        if($points <= 0) { return; }
        if($user_id == NULL)
        {
            if(isset($this->data['user_id']))
            {
                $user_id = $this->data['user_id'];
            }
            else { return false; }
        }
        $user_points = $this->users->user_info($user_id, 'points');
        $user_score = $this->users->user_info($user_id, 'score');

        $score_sql = "";
        if($remove_from_score)
        {
            $user_score = $user_score - $points;
            if($user_score < 0) { $user_score = 0; }
            $score_sql = "`score`=$user_score,";
        }

        $user_points = $user_points-$points;
        if($user_points < 0) { $user_points = 0; }

        $query = "UPDATE `users` SET $score_sql `points`=$user_points WHERE `id`=$user_id";
        $this->db->query($query);

        return true;
    }
}


/* End of file /models/points.php */
/* Location: ./application/modules/account/models/points.php */
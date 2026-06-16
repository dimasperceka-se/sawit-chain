<?php

class Mvideo extends CI_Model {

    function readVideo($key, $start, $limit) {
        $sql = "
            SELECT %s
            FROM ktv_video
            LEFT JOIN sys_user ON CreatedBy=UserId
            WHERE (VideoTitle like ? OR VideoDescription like ?) AND ktv_video.StatusCode != 'nullified'
            ORDER BY DateCreated DESC %s";
        $query = $this->db->query(sprintf($sql, '*,UserName,REPLACE(REPLACE(REPLACE(FORMAT(VideoSize/1024, 2), ".", "@"), ",", "."), "@", ",") as VideoSize
         ', 'LIMIT ?,?'), array("%$key%", "%$key%", (int) $start, (int) $limit));
        $result['data'] = $query->result_array();
        $query = $this->db->query(sprintf($sql, 'count(*) as total', ''), array("%$key%", "%$key%"));
        $result['total'] = $query->row()->total;
        return $result;
    }

    function readDetailVideo($id) {
        $sql = "
            SELECT
                a.VideoId,
                a.VideoFile,
                a.VideoTitle,
                a.VideoDescription,
                a.VideoThumbnail,
                a.VideoSize,
                a.DateCreated,
                a.CreatedBy
            FROM ktv_video a
            WHERE a.VideoID = ?
        ";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        $return['data'] = $result[0];
        //return $return;
        return $result[0];
    }

    function createVideo($file, $VideoTitle, $VideoDescription, $VideoThumbnail, $VideoSize, $userId) {
        $sql = "
            INSERT INTO ktv_video(VideoFile, VideoTitle, VideoDescription, VideoThumbnail, VideoSize, DateCreated, CreatedBy)
            VALUES (?,?,?,?,?,now(),$userId)";
        $query = $this->db->query($sql, array($file, $VideoTitle, $VideoDescription, $VideoThumbnail, $VideoSize));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "record created.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to create record";
        }
        return $results;
    }

    function updateVideo($VideoID, $file, $VideoTitle, $VideoDescription, $VideoThumbnail, $VideoSize, $userId) {
        $sql = "
            UPDATE
                `ktv_video`
            SET
                `VideoFile` = ?,
                `VideoTitle` = ?,
                `VideoDescription` = ?,
                `VideoThumbnail` = ?,
                `VideoSize` = ?,
                `DateCreated` = ?,
                `CreatedBy` = ?
            WHERE `VideoId` = ?
        ";
        $query = $this->db->query($sql, array($file, $VideoTitle, $VideoDescription, $VideoThumbnail, $VideoSize, date('Y-m-d H:i:s'), $userId, $VideoID));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "data updated.";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to update data";
        }
        return $query;
    }

    function deleteVideo($id) {
        //$sql = "DELETE FROM ktv_video WHERE VideoId=?";
        $sql="UPDATE ktv_video SET StatusCode='nullified',UpdatedBy='".$_SESSION['userid']."',DateUpdated=NOW() WHERE VideoId = ? LIMIT 1";
        $query = $this->db->query($sql, array($id));
        if ($query) {
            $results['success'] = true;
            $results['message'] = "DELETED";
        } else {
            $results['success'] = false;
            $results['message'] = "Failed to delete record";
        }
        return $results;
    }

}

?>

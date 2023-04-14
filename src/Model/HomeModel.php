<?php

namespace App\Model;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeModel extends  AbstractController
{

    /**
     * 
     * Add music to user favourite music list.
     * 
     * @param $data
     * Contains mix data of favourite music and user.
     * 
     * @param $em
     * Contains instance of enitity manager variable.
     * 
     * @return boolean
     * 
     */
    public function isAddedToFav($data, $em)
    {
        $em->persist($data);
        if ($em->flush() == null)
            return true;
        else
            return false;
    }

    /**
     * 
     * Returns a list of user favourite music list.
     * 
     * @param $em
     * Contains instance of enitity manager variable.
     * 
     * @return mixed
     * List of user favourite music.
     * 
     */
    public function getFavouritesList($em)
    {

        $session = new Session();
        $user_id = $session->get('uid');

        $RAW_QUERY = 'SELECT * FROM music_table 
         INNER JOIN favourite_table ON music_table.id=favourite_table.music_id 
         WHERE favourite_table.user_id = :user_id';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('user_id', $user_id);
        return $statement->execute()->fetchAll();
    }

    /**
     * 
     * Add new music data to the music table.
     * 
     * @param $em
     * Contains instance of enitity manager variable.
     * 
     * @param $model
     * Contains instance music data model.
     * 
     * @return boolean
     * Return ture if upload is done or false.
     * 
     */
    public function isUploadDone($model, $em): bool
    {
        $em->persist($model);
        if ($em->flush() == null) {
            return true;
        } else {
            return false;
        }
    }
}

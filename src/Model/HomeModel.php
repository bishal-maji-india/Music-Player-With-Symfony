<?php

namespace App\Model;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeModel extends  AbstractController
{
    public function isAddedToFav($article,$em)
    {
        $em->persist($article);
        if($em->flush()==null)
            return true;
        else
            return false;


    }
    public function getFavouritesList($em)
    {

        $session=new Session();
        $user_id=$session->get('uid');

        $RAW_QUERY = 'SELECT * FROM music_table 
         INNER JOIN favourite_table ON music_table.id=favourite_table.music_id 
         WHERE favourite_table.user_id = :user_id';

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        // Set parameters
        $statement->bindValue('user_id', $user_id);
        return $statement->execute()->fetchAll();


    }
    public function isUploadDone($model,$em): bool
    {
        $em->persist($model);
        if($em->flush()==null){
            return true;
        }else{
            return false;
        }
    }
}
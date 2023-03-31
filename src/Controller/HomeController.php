<?php

namespace App\Controller;

use App\Entity\FavouriteTable;
use App\Entity\MusicTable;
use App\Entity\UpdateValidation;
use App\Entity\UserTable;
use App\Model\AuthModel;
use App\Model\HomeModel;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeController extends AbstractController
{

    /**
     * @Route("update_profile", name="profile_update_route")
     */
    public function update_profile(EntityManagerInterface $entityManager, Request $request,ValidatorInterface $validator)
    {
        $error_arr=array();
        $form = $this->createFormBuilder([])
            ->add('email', TextType::class, ['label' => 'Email'])
            ->add('contact', TextType::class, ['label' => 'Contact'])
            ->add('interest', ChoiceType::class, ['choices' => ['Rock' => 1, 'Melody' => 2, 'Soft' => 3], 'label' => 'Genre', 'required' => true])
            ->add('update', SubmitType::class, ['label' => 'Update'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $article = new UpdateValidation();
            $data = $form->getData();
            $article->setEmail($data['email']);
            $article->setContact($data['contact']);
            $article->setInterest($data['interest']);

            $errors = $validator->validate($article);//form field validation
            if (count($errors) > 0) {
                $i = 0;
                foreach ($errors as $error) {
                    $error_arr[$i] = $error->getMessage();
                    $i = $i + 1;
                }
            }else{
                $session = new Session();
                $id = $session->get('uid');

                $user = $entityManager->getRepository(UserTable::class)->find($id);

                if (!$user) {
                    throw $this->createNotFoundException(
                        'No product found for id ' . $id
                    );
                }
                $arr = $form->getData();
                $user->setEmail($arr['email']);
                $user->setContact($arr['contact']);
                $user->setInterest($arr['interest']);

                $entityManager->persist($user);
                if ($entityManager->flush() == null) {
                    return $this->redirectToRoute('success_route', ['message' => 'Profile Page Updated', 'nav_route' => 'home_page_route']);
                } else {
                    return $this->redirectToRoute('error_route', ['message' => 'Profile Page Updated', 'nav_route' => 'profile_update_route']);
                }
            }

        }
        return $this->render('home/update_profile.html.twig', ['update_profile_form' => $form->createView(),'errors'=>$error_arr]);


    }

    /**
     * @Route("/add_music",name="add_music_route")
     * @param Request $request ;
     *
     */
    public function add_music(EntityManagerInterface $entityManager, Request $request)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $add_music_form = $this->createFormBuilder([])
            ->add('audio', FileType::class, ['label' => 'Audio'])
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('singer', TextType::class, ['label' => 'Singer'])
            ->add('genre', ChoiceType::class, ['label' => 'Genre', 'choices' => array('Rock' => '0', 'Melody' => '1', 'Soft' => '2')])
            ->add('thumb', FileType::class, ['label' => 'Thumbnail'])
            ->add('submit_music', SubmitType::class, ['label' => 'Upload Audio'])
            ->getForm();
        $add_music_form->handleRequest($request);

        if ($add_music_form->isSubmitted() and $add_music_form->isValid()) {
            $data = $add_music_form->getData();
            $model = new HomeModel();

            $article = new MusicTable();
            $article->setName($data['name']);
            $article->setAudio($data['audio']);
            $article->setSinger($data['singer']);
            $article->setGenre($data['genre']);
            $article->setThumb($data['thumb']);
            $session = new Session();
            $param_upload_by = $session->get('uid');
            $article->setUploadBy($param_upload_by);

            $audio_file = $add_music_form->get('audio')->getData();
            $thumb_file = $add_music_form->get('thumb')->getData();
            $audio_fileName = md5(uniqid()) . '.' . $audio_file->guessExtension();
            $thumb_fileName = md5(uniqid()) . '.' . $thumb_file->guessExtension();

            $audio_file->move($this->getParameter('audio_directory'), $audio_fileName);
            $thumb_file->move($this->getParameter('thumb_directory'), $thumb_fileName);
            $article->setAudio($audio_fileName);
            $article->setThumb($thumb_fileName);

            if ($model->isUploadDone($article, $entityManager, $audio_file, $thumb_file)) {
                return $this->redirectToRoute('success_route', ['message' => 'Audio file added successfully', 'nav_route' => 'home_page_route']);
            } else {
                return $this->redirectToRoute('error_route', ['message' => 'Upload Failed', 'nav_route' => 'home_page_route']);
            }
        }
        return $this->render('home/upload_music.html.twig', ['add_music_form' => $add_music_form->createView(), 'row_attr' => array('class' => 'flex')]);

    }


    /**
     * @Route("/home", name="home_page_route")
     */
    public function home(EntityManagerInterface $entityManager, Request $request): Response
    {
        $session = new Session();
        $session->start();
        $login = $session->get('login');
        if (!$login) {
            return $this->redirectToRoute('index');
        }
        //home buttons form
        $form = $this->createFormBuilder([])
            ->add('add_music', SubmitType::class, ['label' => 'Add Music'])
            ->add('logout', SubmitType::class, ['label' => 'Logout'])
            ->add('profile_update', SubmitType::class, ['label' => 'Profile Update'])
            ->add('my_favourites', SubmitType::class, ['label' => 'My Favourites'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            if ($form->get('add_music')->isClicked()) {
                return $this->redirectToRoute('add_music_route');
            }
            if ($form->get('logout')->isClicked()) {
                return $this->redirectToRoute('logout_route');
            }
            if ($form->get('profile_update')->isClicked()) {
                return $this->redirectToRoute('profile_update_route');
            }
            if ($form->get('my_favourites')->isClicked()) {
                return $this->redirectToRoute('my_favourites_route');
            }

        }

        $music_list_repo = $entityManager->getRepository(MusicTable::class);

        $music_list = $music_list_repo->findAll();
        $uid = $session->get('uid');
        return $this->render('home/home_page.html.twig', ['attr' => ['class' => 'flex'], 'music_list' => $music_list, 'home_buttons_form' => $form->createView(), 'uid' => $uid]);
    }

    /**
     *
     * @Route("/favourite{music_id}", name="add_favourite_route")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $music_id
     *
     */
    public function add_to_favourite(EntityManagerInterface $entityManager, Request $request, $music_id)
    {
        $session = new Session();
        $session->start();
        $userid = $session->get('uid');
        $model = new HomeModel();
        $article = new FavouriteTable();
        $article->setUserId($userid);
        $article->setMusicId($music_id);

        if ($model->isAddedToFav($article, $entityManager)) {
            return $this->redirectToRoute('success_route', ['message' => 'added to fav', 'nav_route' => 'home_page_route']);
        } else {
            return $this->redirectToRoute('error_route', ['message' => 'Operation Failed', 'nav_route' => 'home_page_route']);
        }

    }

    /**
     * @Route("/my_favourites", name="my_favourites_route")
     */
    public function my_favourites(EntityManagerInterface $entityManager)
    {
        $model = new HomeModel();
        $favourite_arr = $model->getFavouritesList($entityManager);
        return $this->render('home/favourite_page.html.twig', ['favourite_arr' => $favourite_arr]);

    }


    /**
     *
     * @Route("/open_player/{id}/{name}/{singer}/{genre}/{thumb}/{audio}", name="open_player_route")
     * @param $id
     * @param $name
     * @param $singer
     * @param $genre
     * @param $thumb
     * @param  $audio
     * @return Response
     */
    public function open_player($id, $name, $singer, $genre, $thumb, $audio): Response
    {
        return $this->render('home/player_view.html.twig', ['id' => $id, 'name' => $name, 'singer' => $singer, 'genre' => $genre, 'thumb' => $thumb, 'audio' => $audio,]);

    }

}

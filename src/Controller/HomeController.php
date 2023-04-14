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

/**
 * Controlls all operation starting from homepage like- update profile,
 * add music, favourite, list music, update music.
 * 
 */
class HomeController extends AbstractController
{
    /**
     * Variable to check if user is loged in or not.
     * this varialbe controlls page navigtion.
     */
    public $session;


    /**
     * Constructor function to start new session.
     */
    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Updates user profile.
     * 
     * @Route("update_profile", name="profile_update_route")
     * 
     * @param Request $request
     * Instance of request variable.
     * 
     * @param ValidatorInterface $validatior.
     * Pre-defined symfony class which validates form data.
     * 
     * @param EntityManagerInterface $entityMangaer
     * Instance of entity manager interface.
     * 
     * 
     */
    public function update_profile(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator)
    {
        // Store the error message present after form validation.
        $error_arr = array();

        // Creates form to update user data.
        $form = $this->createFormBuilder([])
            ->add('email', TextType::class, ['label' => 'Email'])
            ->add('contact', TextType::class, ['label' => 'Contact'])
            ->add('interest', ChoiceType::class, ['choices' => ['Rock' => 1, 'Melody' => 2, 'Soft' => 3], 'label' => 'Genre', 'required' => true])
            ->add('update', SubmitType::class, ['label' => 'Update'])
            ->getForm();

        $form->handleRequest($request);

        //Handle data after form submission.
        if ($form->isSubmitted() and $form->isValid()) {

            //instance of model class to validate and update user data.
            $update_validator = new UpdateValidation();

            $data = $form->getData();
            $update_validator->setEmail($data['email']);
            $update_validator->setContact($data['contact']);
            $update_validator->setInterest($data['interest']);

            // Validate the form using pre-defined symfony validator clsss.
            $errors = $validator->validate($update_validator);

            // Block excute if there is error in form after validation.
            if (count($errors) > 0) {
                $i = 0;
                foreach ($errors as $error) {
                    $error_arr[$i] = $error->getMessage();
                    $i = $i + 1;
                }
            } else {
                $id = $this->session->get('uid');
                $user = $entityManager->getRepository(UserTable::class)->find($id);

                if (!$user) {
                    throw $this->createNotFoundException(
                        'No User found for id ' . $id
                    );
                }
                // Get the data of form
                $arr = $form->getData();
                $user->setEmail($arr['email']);
                $user->setContact($arr['contact']);
                $user->setInterest($arr['interest']);

                $entityManager->persist($user);

                // Go to success page if data insterted in table or error page.
                if ($entityManager->flush() == null) {
                    return $this->redirectToRoute('success_route', ['message' => 'Profile Page Updated', 'nav_route' => 'home_page_route']);
                } else {
                    return $this->redirectToRoute('error_route', ['message' => 'Profile Page Updated', 'nav_route' => 'profile_update_route']);
                }
            }
        }
        return $this->render('home/update_profile.html.twig', ['update_profile_form' => $form->createView(), 'errors' => $error_arr]);
    }

    /**
     * Adds music to the database.
     * 
     * @Route("/add_music",name="add_music_route")
     * 
     * @param Request $request
     * Instance of request variable.
     * 
     * @param EntityManagerInterface $entityMangaer
     * Instance of entity manager interface.
     *
     */
    public function add_music(EntityManagerInterface $entityManager, Request $request)
    {
        $add_music_form = $this->createFormBuilder([])
            ->add('audio', FileType::class, ['label' => 'Audio'])
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('singer', TextType::class, ['label' => 'Singer'])
            ->add('genre', ChoiceType::class, ['label' => 'Genre', 'choices' => array('Rock' => '0', 'Melody' => '1', 'Soft' => '2')])
            ->add('thumb', FileType::class, ['label' => 'Thumbnail'])
            ->add('submit_music', SubmitType::class, ['label' => 'Upload Audio'])
            ->getForm();

        $add_music_form->handleRequest($request);

        //Handle form data after submission.
        if ($add_music_form->isSubmitted() and $add_music_form->isValid()) {
            $data = $add_music_form->getData();

            // Instance of home model
            $model = new HomeModel();

            // Instance of music table entity.
            $music_table = new MusicTable();

            // Get the user id form session variable.
            $param_upload_by = $this->session->get('uid');

            $music_table->setName($data['name']);
            $music_table->setAudio($data['audio']);
            $music_table->setSinger($data['singer']);
            $music_table->setGenre($data['genre']);
            $music_table->setThumb($data['thumb']);
            $music_table->setUploadBy($param_upload_by);

            $audio_file = $add_music_form->get('audio')->getData();
            $thumb_file = $add_music_form->get('thumb')->getData();
            $audio_fileName = md5(uniqid()) . '.' . $audio_file->guessExtension();
            $thumb_fileName = md5(uniqid()) . '.' . $thumb_file->guessExtension();
            $audio_file->move($this->getParameter('audio_directory'), $audio_fileName);
            $thumb_file->move($this->getParameter('thumb_directory'), $thumb_fileName);

            $music_table->setAudio($audio_fileName);
            $music_table->setThumb($thumb_fileName);

            if ($model->isUploadDone($music_table, $entityManager, $audio_file, $thumb_file)) {
                return $this->redirectToRoute('success_route', ['message' => 'Audio file added successfully', 'nav_route' => 'home_page_route']);
            } else {
                return $this->redirectToRoute('error_route', ['message' => 'Upload Failed', 'nav_route' => 'home_page_route']);
            }
        }
        return $this->render('home/upload_music.html.twig', ['add_music_form' => $add_music_form->createView(), 'row_attr' => array('class' => 'flex')]);
    }


    /**
     * List all music available in db.
     * 
     * @Route("/home", name="home_page_route")
     * 
     * @param Request $request
     * Instance of request variable.
     * 
     * @param EntityManagerInterface $entityMangaer
     * Instance of entity manager interface.
     * 
     * 
     */
    public function home(EntityManagerInterface $entityManager, Request $request): Response
    {


        $login = $this->session->get('login');
        if (!$login) {
            return $this->redirectToRoute('index');
        }
        // Form for buttons present in home page.
        $form = $this->createFormBuilder([])
            ->add('add_music', SubmitType::class, ['label' => 'Add Music'])
            ->add('logout', SubmitType::class, ['label' => 'Logout'])
            ->add('profile_update', SubmitType::class, ['label' => 'Profile Update'])
            ->add('my_favourites', SubmitType::class, ['label' => 'My Favourites'])
            ->getForm();

        $form->handleRequest($request);

        // Handle the form submission and click event.
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

        // Returns List of all music present in db.
        $music_list = $music_list_repo->findAll();

        //Get user id from session variable.
        $uid = $this->session->get('uid');
        return $this->render('home/home_page.html.twig', ['attr' => ['class' => 'flex'], 'music_list' => $music_list, 'home_buttons_form' => $form->createView(), 'uid' => $uid]);
    }

    /**
     * Add music to user favourite list.
     * @Route("/favourite{music_id}", name="add_favourite_route")
     * 
     * @param EntityManagerInterface $entityManager
     * Instance of entitiy manager interface variable.
     * 
     * @param Request $request
     * Instance of request.
     * 
     * @param $music_id
     * Id of row having same music id.
     *
     */
    public function add_to_favourite(EntityManagerInterface $entityManager, Request $request, $music_id)
    {
        // Get uid from session variable.
        $userid = $this->session->get('uid');
        $model = new HomeModel();

        // instance of FavoutieTable class
        $favourite_tabe = new FavouriteTable();
        $favourite_tabe->setUserId($userid);
        $favourite_tabe->setMusicId($music_id);

        // If favoutie is added go to success page or error page.
        if ($model->isAddedToFav($favourite_tabe, $entityManager)) {
            return $this->redirectToRoute('success_route', ['message' => 'added to fav', 'nav_route' => 'home_page_route']);
        } else {
            return $this->redirectToRoute('error_route', ['message' => 'Operation Failed', 'nav_route' => 'home_page_route']);
        }
    }

    /**
     * List all the favourite music in different page.
     * 
     * @Route("/my_favourites", name="my_favourites_route")
     * 
     * @param EntityManagerInterface $entityManager
     * Instance of entity manager interface.
     * 
     */
    public function my_favourites(EntityManagerInterface $entityManager)
    {
        //Instance of home model.
        $model = new HomeModel();

        // Returns array list of all music in user favourite list.
        $favourite_arr = $model->getFavouritesList($entityManager);
        return $this->render('home/favourite_page.html.twig', ['favourite_arr' => $favourite_arr]);
    }

    /**
     * Open music player page with music data passed from homepage.
     * 
     * @Route("/open_player/{id}/{name}/{singer}/{genre}/{thumb}/{audio}", name="open_player_route")
     * 
     * @param $id
     * Instance of id of the song.
     * 
     * @param $name
     * Instance of name of the song.
     * 
     * @param $singer
     * Instance of name of the song.
     * 
     * @param $genre
     * Instance of genre of the song.
     * 
     * @param $thumb
     * Instance of thumbnail of the song.
     * 
     * @param  $audio
     * Instance of link of the song.
     * 
     * @return Response
     * Instance of response variable.
     * 
     */
    public function open_player($id, $name, $singer, $genre, $thumb, $audio): Response
    {
        return $this->render('home/player_view.html.twig', ['id' => $id, 'name' => $name, 'singer' => $singer, 'genre' => $genre, 'thumb' => $thumb, 'audio' => $audio,]);
    }
}

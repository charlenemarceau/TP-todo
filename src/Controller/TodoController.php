<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Todo;
use App\Form\TodoFormType;
use App\Repository\CategoryRepository;
use App\Repository\TodoRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TodoController extends AbstractController
{
    private $categories;

    function __construct(CategoryRepository $repo)
    {
        $this->categories = $repo->findAll();
    }


     /**
     * @Route("/", name="app_todo")
     */
    public function index(TodoRepository $repo): Response
    {
        // $todos = $this->getDoctrine()->getRepository(Todo::class)->findAll();
        $todos = $repo ->findAll();
        //dd($todos);
        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
            'categories'=> $this->categories
        ]);
    }

    /**
     * @Route("/create", name="app_todo_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em)
    {
        #etape affichage (GET)
        $todo = new Todo;
        $form = $this->createForm(TodoFormType::class, $todo);
        # etape soumission (POST)
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // ancienne méthode que l'on trouve dans des anciennnes ressources
            // $this->getDoctrine()->getManager()->persist($todo);
            $em->persist($todo);
            $em ->flush();
            return $this->redirectToRoute('app_todo');

        }
        return $this->render('todo/create.html.twig', [
            'formTodo'=> $form -> createView(),  
            'categories'=> $this->categories
        ]);
    }

    /**
         * @Route("/detail/{id}", name="app_todo_detail", methods={"GET"})
         */
        public function detail($id, TodoRepository $repo): Response 
        {
            // $todo = $this->getDoctrine()->getRepository(Todo::class)
            $todo = $repo->find($id);
            //dd($todo);
            return $this->render('todo/todo.html.twig', [
                'todo' => $todo,
                'categories'=> $this->categories
             ]);
        }


        /**
         * paramconverter => correspondance entre un id dans la route et un objet du type Todo
         * @Route("/todo/{id}/edit", name="app_todo_edit", methods={"GET", "POST"})
         */
        public function edit(Todo $todo, Request $request, EntityManagerInterface $em): Response 
        {
        
           // dd($todo);
        $form = $this->createForm(TodoFormType::class, $todo);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            #update
            $todo->setUpdatedAt(new DateTime('now'));
            $em ->flush();
            # crée un message flash (session flash)
            $this->addFlash(
                'info',
                'Vos modifications ont été enregistré avec succès !'
            );
            #on revient sur la même page (la méthode GET)
            return $this->redirectToRoute('app_todo_edit', ['id' => $todo->getId()]);
        }

        return $this->render('todo/edit.html.twig', [
            'formTodo' => $form->createView(),
            'todo' => $todo,
            'categories'=> $this->categories
            ]);
        }

        /**
         * @Route("/todo/{id}/delete", name="app_todo_delete")
         */
        public function delete(Todo $todo, EntityManagerInterface $em) {
            $em-> remove($todo);
            $em -> flush();
            return $this -> redirectToRoute('app_todo');
        }
   
         /**
         * @Route("/todo/{id}/deletecrsf", name="app_todo_delete_csrf", methods={"DELETE"})
         * 
         * $request-request->get()   : POST
         * $request->query->get()     : GET
         */
        public function delete2(Todo $todo, EntityManagerInterface $em, Request $request) {
            $sumbmittedToken = $request->request->get('token');
            //dd($sumbmittedToken);
            if($this->isCsrfTokenValid('delete-item', $sumbmittedToken)) {
                $em-> remove($todo);
                $em -> flush();
            }
            return $this -> redirectToRoute('app_todo');
        }

        /**
         * @Route("/category/{id}", name="app_todo_category")
         *
         * @param Category $cat
         * @return void
         */
        public function todoByCategory(Category $cat) : Response 
        {
            return $this->render('/todo/index.html.twig', [
                'todos' => $cat->getTodos(),
                'categories' => $this->categories
            ]);
        }
}

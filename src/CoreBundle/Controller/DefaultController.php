<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use CoreBundle\Entity\Article;
use CoreBundle\Form\ArticleType;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreBundle:Default:index.html.twig');
    }

    public function categoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $listArticles = $em->getRepository('CoreBundle:Article') ->getArticleWithCategoryId($id);
        $category = $em->getRepository('CoreBundle:Category')->find($id);

        // Si l'annonce n'existe pas, on affiche une erreur 404
        if ($category == null) {
          throw $this->createNotFoundException("La categorie d'id ".$id." n'existe pas.");
        }

        return $this->render('CoreBundle:category.html.twig', array('category' => $category,
            'articles' => $listArticles));
    }

    public function articleAction($id)
    {
        return $this->render('CoreBundle:article.html.twig');
    }

    public function addAction(Request $request)
      {
        $article = new Article();
        $form = $this->get('form.factory')->create(ArticleType::class, $article);

        if ($form->handleRequest($request)->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($article);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistré.');

          return $this->redirect($this->generateUrl('core_article', array('id' => $article->getId())));
        }

        return $this->render('CoreBundle:add.html.twig', array(
          'form' => $form->createView(),
        ));
      }
}

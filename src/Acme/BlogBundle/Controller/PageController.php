<?php

namespace Acme\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Acme\BlogBundle\Exception\InvalidFormException;
use Acme\BlogBundle\Form\PageType;
use Acme\BlogBundle\Model\PageInterface;


class PageController extends FOSRestController
{
    /**
     * Get single Page,
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Page for a given id",
     *   output = "Acme\BlogBundle\Entity\Page",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the page is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="page")
     *
     * @param int     $id      the page id
     *
     * @return array
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function getPageAction($id)
    {
        $page = $this->getOr404($id);

        return $page;
    }

    /**
     * Presents the form to use to create a new page.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @return FormTypeInterface
     */
    public function newPageAction()
    {
        return $this->createForm(new PageType());
    }


    /**
     * Create a Page from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new page from the submitted data.",
     *   input = "Acme\BlogBundle\Form\PageType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AcmeBlogBundle:Page:newPage.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST
     * )
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postPageAction()
    {
        try {
            $newPage = $this->container->get('acme_blog.page.handler')->post(
                    $this->container->get('request')->request->all()
            );

            $routeOptions = array(
                'id' => $newPage->getId(),
                '_format' => $this->container->get('request')->get('_format')
            );

            return $this->routeRedirectView('api_1_get_page', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return array('form' => $exception->getForm());
        }
    }

    /**
     * Fetch a Page or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return PageInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($page = $this->container->get('acme_blog.page.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $page;
    }
}

<?php

namespace App\Controller;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_main')]
class ProjectController extends AbstractController
{
    #[Route('/project', name: 'project_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Project::class)->findAll();
        $data = [];
        foreach($products as $product) {
            $data[] = [
                'id'=> $product->getId(),
                'name'=>$product->getName(),
                'description'=>$product->getDescription(),
            ];
        }
        return $this->json($data);
    }

   #[Route('/project', name: 'project_new', methods: ['POST'])]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $project = new Project();
        $project->setName($request->request->get('name'));
        $project->setDescription($request->request->get('description'));
        $em->persist($project);
        $em->flush();

        return $this->json('Created new project succesfully with id ' .$project->getId());  
    }

    #[Route('/project/{id}', name: 'project_show', methods: ['GET'])]
    public function show(int $id, ManagerRegistry $doctrine): Response
    {
        $project = $doctrine->getRepository(Project::class)->find($id);
        if (!$project) {
            return $this->json('No project found for id ' .$id, 404);
        }
        $data = [
                'id'=> $project->getId(),
                'name'=>$project->getName(),
                'description'=>$project->getDescription(),
            ];
        return $this->json($data);
    }

    #[Route('/project/{id}', name: 'project_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, int $id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $project = $em->getRepository(Project::class)->find($id);
           if (!$project) {
            return $this->json('No project found for id ' .$id, 404);
        }
        $content = json_decode($request->getContent());
        $project->setName($content->name);
        $project->setDescription($content->description);
        $em->flush();
      
        $data = [
                'id'=> $project->getId(),
                'name'=>$project->getName(),
                'description'=>$project->getDescription(),
            ];
        return $this->json($data);
    }

    #[Route('/project/{id}', name: 'project_delete', methods: ['DELETE'])]
    public function delete(int $id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $project = $em->getRepository(Project::class)->find($id);
         if (!$project) {
            return $this->json('No project found for id ' .$id, 404);
        }
        $em->remove($project);
        $em->flush();

        return $this->json('Deleted a project succesfully with id ' .$id);

    }
}
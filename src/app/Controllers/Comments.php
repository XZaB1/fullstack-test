<?php

namespace App\Controllers;

use App\Models\CommentModel;
use CodeIgniter\Controller;

class Comments extends Controller
{
    // GET /comments — JSON-список с пагинацией и сортировкой
    public function index()
    {
        $model   = new CommentModel();
        $sortBy  = $this->request->getGet('sort') ?? 'id';
        $sortDir = $this->request->getGet('dir')  ?? 'desc';
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));
        $perPage = 3;

        // whitelist для безопасности
        if (!in_array($sortBy, ['id', 'date'], true))   $sortBy  = 'id';
        if (!in_array($sortDir, ['asc', 'desc'], true)) $sortDir = 'desc';

        $total  = $model->countAllResults();
        $offset = ($page - 1) * $perPage;
        $items  = $model->orderBy($sortBy, $sortDir)
                        ->findAll($perPage, $offset);

        return $this->response->setJSON([
            'items'      => $items,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ]);
    }

    // POST /comments — добавление комментария
    public function create()
    {
        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'text' => trim((string) $this->request->getPost('text')),
            'date' => trim((string) $this->request->getPost('date')),
        ];

        $rules = [
            'name' => 'required|valid_email|max_length[255]',
            'text' => 'required|max_length[5000]',
            'date' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['errors' => $this->validator->getErrors()]);
        }

        $model = new CommentModel();
        $id    = $model->insert($data);

        return $this->response->setJSON([
            'success' => true,
            'item'    => $model->find($id),
        ]);
    }

    // DELETE /comments/{id}
    public function delete($id = null)
    {
        $model = new CommentModel();

        if (!$model->find($id)) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['error' => 'Комментарий не найден']);
        }

        $model->delete($id);

        return $this->response->setJSON(['success' => true]);
    }

    // GET / — HTML-страница со списком и формой
    public function page()
    {
        return view('comments/index');
    }
}
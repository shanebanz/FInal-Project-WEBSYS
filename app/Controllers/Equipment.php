<?php

namespace App\Controllers;

use App\Models\EquipmentModel;
use App\Models\EquipmentCategoryModel;

class Equipment extends BaseController
{
    protected $equipmentModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->equipmentModel = new EquipmentModel();
        $this->categoryModel = new \App\Models\EquipmentCategoryModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $perPage = 10;
        $data = [
            'title' => 'Equipment Management',
            'equipment' => $this->equipmentModel->getEquipmentWithCategory(),
            'pager' => $this->equipmentModel->pager
        ];

        return view('equipment/index', $data);
    }

    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Add Equipment',
            'categories' => $this->categoryModel->findAll()
        ];

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'equipment_id' => 'required|is_unique[equipment.equipment_id]',
                'category_id' => 'required',
                'name' => 'required|min_length[3]',
                'total_quantity' => 'required|integer|greater_than_equal_to[0]',
                'image' => 'uploaded[image]|max_size[image,2048]|is_image[image]'
            ];

            if ($this->validate($rules)) {
                $image = $this->request->getFile('image');
                $imageName = null;

                if ($image && $image->isValid()) {
                    $imageName = $image->getRandomName();
                    $image->move(WRITEPATH . '../public/uploads/equipment', $imageName);
                    
                    // Create thumbnail
                    $this->createThumbnail($imageName);
                }

                $equipmentData = [
                    'equipment_id' => $this->request->getPost('equipment_id'),
                    'category_id' => $this->request->getPost('category_id'),
                    'name' => $this->request->getPost('name'),
                    'description' => $this->request->getPost('description'),
                    'total_quantity' => $this->request->getPost('total_quantity'),
                    'available_quantity' => $this->request->getPost('total_quantity'),
                    'status' => 'active',
                    'image' => $imageName
                ];

                if ($this->equipmentModel->insert($equipmentData)) {
                    session()->setFlashdata('success', 'Equipment added successfully!');
                    return redirect()->to('/equipment');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('equipment/create', $data);
    }

    private function createThumbnail($imageName)
    {
        $imagePath = WRITEPATH . '../public/uploads/equipment/' . $imageName;
        $thumbnailPath = WRITEPATH . '../public/uploads/equipment/thumbnails/' . $imageName;

        // Create thumbnails directory if it doesn't exist
        if (!is_dir(WRITEPATH . '../public/uploads/equipment/thumbnails')) {
            mkdir(WRITEPATH . '../public/uploads/equipment/thumbnails', 0755, true);
        }

        $imageInfo = getimagesize($imagePath);
        $imageType = $imageInfo[2];

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($imagePath);
                break;
            default:
                return false;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $thumbWidth = 200;
        $thumbHeight = ($height / $width) * $thumbWidth;

        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumb, $thumbnailPath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumb, $thumbnailPath, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumb, $thumbnailPath);
                break;
        }

        imagedestroy($source);
        imagedestroy($thumb);
        return true;
    }

    public function edit($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $equipment = $this->equipmentModel->find($id);
        
        if (!$equipment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Edit Equipment',
            'equipment' => $equipment,
            'categories' => $this->categoryModel->findAll()
        ];

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'equipment_id' => "required|is_unique[equipment.equipment_id,id,{$id}]",
                'category_id' => 'required',
                'name' => 'required|min_length[3]',
                'total_quantity' => 'required|integer|greater_than_equal_to[0]'
            ];

            if ($this->validate($rules)) {
                $image = $this->request->getFile('image');
                $imageName = $equipment['image'];

                if ($image && $image->isValid()) {
                    // Delete old image
                    if ($imageName && file_exists(WRITEPATH . '../public/uploads/equipment/' . $imageName)) {
                        unlink(WRITEPATH . '../public/uploads/equipment/' . $imageName);
                        unlink(WRITEPATH . '../public/uploads/equipment/thumbnails/' . $imageName);
                    }

                    $imageName = $image->getRandomName();
                    $image->move(WRITEPATH . '../public/uploads/equipment', $imageName);
                    $this->createThumbnail($imageName);
                }

                $equipmentData = [
                    'equipment_id' => $this->request->getPost('equipment_id'),
                    'category_id' => $this->request->getPost('category_id'),
                    'name' => $this->request->getPost('name'),
                    'description' => $this->request->getPost('description'),
                    'total_quantity' => $this->request->getPost('total_quantity'),
                    'image' => $imageName
                ];

                if ($this->equipmentModel->update($id, $equipmentData)) {
                    session()->setFlashdata('success', 'Equipment updated successfully!');
                    return redirect()->to('/equipment');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('equipment/edit', $data);
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $equipment = $this->equipmentModel->find($id);
        
        if ($equipment) {
            // Soft delete - set status to inactive
            $this->equipmentModel->update($id, ['status' => 'inactive']);
            session()->setFlashdata('success', 'Equipment deactivated successfully!');
        }

        return redirect()->to('/equipment');
    }

    public function view($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Equipment Details',
            'equipment' => $this->equipmentModel->getEquipmentWithCategory($id)
        ];

        if (!$data['equipment']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('equipment/view', $data);
    }
}
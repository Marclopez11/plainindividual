<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExcelWordTest extends TestCase
{
    public function test_excel_word_index_page_loads()
    {
        $response = $this->get(route('excel-word.index'));
        $response->assertStatus(200);
        $response->assertViewIs('excel-word.index');
    }

    public function test_template_file_exists()
    {
        $templatePath = storage_path('app/templates/plantilla_plan_soporte_excel.docx');
        $this->assertFileExists($templatePath, 'La plantilla Word no existe en la ruta: ' . $templatePath);
    }

    public function test_process_excel_route_exists()
    {
        $response = $this->post(route('excel-word.process'), [
            // No need to provide a file, we just want to check the route exists
        ]);

        // Should redirect due to validation error, not 404
        $response->assertStatus(302);
        $response->assertSessionHasErrors('excel_file');
    }
}

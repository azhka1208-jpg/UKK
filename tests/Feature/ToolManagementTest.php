<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_create_tool(): void
    {
        $category = Category::create(['name' => 'Alat Uji', 'description' => 'Kategori uji']);

        $response = $this->actingAs($this->admin())->post(route('admin.tools.store'), [
            'code' => 'ELK-100',
            'name' => 'Laptop Uji',
            'category_id' => $category->id,
            'description' => 'Laptop untuk pengujian',
            'stock_total' => 5,
            'condition' => 'baik',
            'location' => 'Rak Uji',
            'status' => 'tersedia',
        ]);

        $response->assertRedirect(route('admin.tools.index'));
        $this->assertDatabaseHas('tools', [
            'code' => 'ELK-100',
            'stock_total' => 5,
            'stock_available' => 5,
        ]);
    }

    public function test_tool_code_must_be_unique(): void
    {
        $category = Category::create(['name' => 'Alat Uji']);
        Tool::create([
            'code' => 'ELK-100',
            'name' => 'Laptop Lama',
            'category_id' => $category->id,
            'stock_total' => 1,
            'stock_available' => 1,
            'condition' => 'baik',
            'status' => 'tersedia',
        ]);

        $this->actingAs($this->admin())->post(route('admin.tools.store'), [
            'code' => 'ELK-100',
            'name' => 'Laptop Baru',
            'category_id' => $category->id,
            'stock_total' => 2,
            'condition' => 'baik',
            'status' => 'tersedia',
        ])->assertSessionHasErrors('code');
    }

    public function test_category_with_tools_cannot_be_deleted(): void
    {
        $category = Category::create(['name' => 'Alat Terpakai']);
        Tool::create([
            'code' => 'ELK-200',
            'name' => 'Alat Terkait',
            'category_id' => $category->id,
            'stock_total' => 1,
            'stock_available' => 1,
            'condition' => 'baik',
            'status' => 'tersedia',
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_stock_total_cannot_be_lower_than_reserved_quantity(): void
    {
        $category = Category::create(['name' => 'Alat Uji']);
        $tool = Tool::create([
            'code' => 'ELK-300',
            'name' => 'Alat Dengan Peminjaman',
            'category_id' => $category->id,
            'stock_total' => 5,
            'stock_available' => 3,
            'condition' => 'baik',
            'status' => 'tersedia',
        ]);

        $this->actingAs($this->admin())
            ->put(route('admin.tools.update', $tool), [
                'code' => 'ELK-300',
                'name' => 'Alat Dengan Peminjaman',
                'category_id' => $category->id,
                'stock_total' => 1,
                'condition' => 'baik',
                'status' => 'tersedia',
            ])
            ->assertSessionHasErrors('stock_total');

        $this->assertDatabaseHas('tools', ['id' => $tool->id, 'stock_total' => 5]);
    }
}
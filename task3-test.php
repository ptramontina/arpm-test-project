<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Models\Product;

class SpreadsheetServiceTest extends TestCase
{
    /**
     * I used this trait that is responsible for deleting the data between each test.
     * It's easier to test with fresh databases.
     */
    use RefreshDatabase;

    /**
     * Tests that the job is pushed onto the corresponding queue.
     *
     * @return void
     *
     * @covers \App\Jobs\ProcessProductImage::__construct
     */
    public function testProcessSpreadsheetQueueIsInserted(): void
    {
        Queue::fake();

        $product = Product::create([
            'product_code' => 1,
            'quantity' => 10,
        ]);

        ProcessProductImage::dispatch($product);

        Queue::assertPushedOn('default', ProcessProductImage::class);
    }

    /**
     * Use a valid file to call the method, and checks if the data is inserted in the Database.
     * 
     * @return void
     */
    public function testProcessSpreadsheetProductIsCreated(): void
    {
        $spreadSheetService = app()->make(SpreadsheetService::class, []);

        $validFilePath = Storage::get('./valid-spreadsheet.xls');        

        $spreadSheetService->processSpreadsheet($validFilePath);        

        $products = Product::all();

        $this->assertTrue($products->isNotEmpty());
    }

    /**
     * Checks if no product was created, passing a invalid file
     * 
     * @return void
     */
    public function testProcessSpreadsheetInvalidFile(): void
    {
        $invalidFilePath = Storage::get('./invalid-spreadsheet.xls'); 
        
        $spreadSheetService->processSpreadsheet($validFilePath);   

        $products = Product::all();

        $this->assertTrue($products->isEmpty());
    }
}

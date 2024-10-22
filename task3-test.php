<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Models\Product;

class SpreadsheetServiceTest extends TestCase
{
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
     * Makes two tests. First with a valid file, and checks if the data is inserted in the Database.
     * After that, tries with an invalid file. And checks if the data is NOT inserted in the Database.
     * 
     * @return void
     */
    public function testProcessSpreadsheetProductIsCreated(): void
    {
        $spreadSheetService = app()->make(SpreadsheetService::class, []);

        $validFilePath = Storage::get('./valid-spreadsheet.xls');        

        $spreadSheetService->processSpreadsheet($validFilePath);        

        $addedProduct = Product::where([
            'product_code' => 1,
            'quantity' => 10,
        ])->first();

        $this->assertNotNull($addedProduct);

        $invalidFilePath = Storage::get('./invalid-spreadsheet.xls'); 
        
        $spreadSheetService->processSpreadsheet($validFilePath);   

        $addedProduct = Product::where([
            'product_code' => 2,
            'quantity' => 20,
        ])->first();

        $this->assertNull($addedProduct);
    }
}

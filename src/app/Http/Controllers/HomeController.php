<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    //Stored here for later deployment to production. Not in use.
    private $TagNamesSWTS = array(
        array('id' => '1','category_name' => 'Manikins','created_at' => NULL,'updated_at' => NULL),
        array('id' => '2','category_name' => 'VideoSystem','created_at' => NULL,'updated_at' => NULL),
        array('id' => '3','category_name' => 'Computers','created_at' => NULL,'updated_at' => NULL),
        array('id' => '4','category_name' => 'Other Equipment','created_at' => NULL,'updated_at' => NULL)
    );

    /**
     *  Returns main inventory page, showing all equipment
     */
    public function index() {
        return view('inventory', [
            'searchTitle' => 'Inventory',
            'categories' => Category::getAllCategories(),
            'equipment' => Equipment::getAllEquipment()
        ]);
    }

    /**
     *  Returns main Inventory page, showing all equipment matching selected tag
     * @param Request $request
     */
    public function indexSearchByTag(Request $request) {
        $data = $request->post();
        $tag = $data['tag'];

        return view('inventory', [
            'searchTitle' => Category::getCategoryName($tag),
            'categories' => Category::getAllCategories(),
            'equipment' => Equipment::getEquipmentByTag($tag)
        ]);
    }

    /**
     * @param Request $request , contains user search input and which columns to match it to. //TODO: Make compatible with new database. First need to implement Calibration table.
     * @returns s Main inventory page, showing all equipment matching search criteria
     */
    public function indexSearchEquipment(Request $request) {
        $data = $request->post();
        $selectedSearch = $data['selectSearch'];
        $userInput = $data['user_input'];

        //If empty search query, return full inventory.
        if (empty($userInput)) return $this->index();

        if ($selectedSearch == 'multiSearch') {
            $equipmentSearchResult = Equipment::getEquipmentByMultiSearch($userInput);
        } else {
            $equipmentSearchResult = Equipment::getEquipmentBySingleSearch($selectedSearch,$userInput);
        }

        return view('inventory', [
            'searchTitle' => 'Result for \''.$userInput.'\'',
            'categories' => Category::getAllCategories(),
            'equipment' => $equipmentSearchResult,
            'highlightText' => $userInput]);
    }
}

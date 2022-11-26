<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHistoryTransactionRequest;
use App\Http\Requests\UpdateHistoryTransactionRequest;
use App\Models\HistoryTransaction;

class HistoryTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreHistoryTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHistoryTransactionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HistoryTransaction  $historyTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(HistoryTransaction $historyTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HistoryTransaction  $historyTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(HistoryTransaction $historyTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHistoryTransactionRequest  $request
     * @param  \App\Models\HistoryTransaction  $historyTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHistoryTransactionRequest $request, HistoryTransaction $historyTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HistoryTransaction  $historyTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(HistoryTransaction $historyTransaction)
    {
        //
    }
}

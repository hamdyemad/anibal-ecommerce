<?php

namespace Modules\AreaSettings\app\Interfaces;

interface CityRepositoryInterface
{
    /**
     * Get all cities with filters and pagination
     */
    public function getAllCities(array $filters = [], int $perPage = 15);

    /**
     * Get cities query for DataTables
     */
    public function getCitiesQuery(array $filters = []);

    /**
     * Get city by ID
     */
    public function getCityById(int $id);

    /**
     * Create a new city
     */
    public function createCity(array $data);

    /**
     * Update city
     */
    public function updateCity(int $id, array $data);

    /**
     * Delete city
     */
    public function deleteCity(int $id);

    /**
     * Get active cities
     */
    public function getActiveCities();

    /**
     * Get cities by country
     */
    public function getCitiesByCountry(int $countryId);
}

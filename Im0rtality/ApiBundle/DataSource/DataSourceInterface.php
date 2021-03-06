<?php
namespace Im0rtality\ApiBundle\DataSource;

interface DataSourceInterface
{
    /**
     * Get elements index (list) from element
     *
     * @param int|null    $limit
     * @param int|null    $offset
     * @param string|null $orderBy
     * @param string|null $order
     * @return array
     */
    public function index($limit = null, $offset = null, $orderBy = null, $order = null);

    /**
     * Read element from collection
     *
     * @param string $identifier
     * @return mixed
     */
    public function read($identifier);

    /**
     * Perform partial update on selected element
     *
     * @param string $identifier
     * @param array  $patch Associative array which is used to update given fields
     * @return mixed
     */
    public function update($identifier, $patch);

    /**
     * Removes element from collection
     *
     * @param string $identifier
     * @return bool
     */
    public function delete($identifier);

    /**
     * Creates element in collection
     *
     * @param array $data Data to create element from
     * @return mixed      Newly created element
     */
    public function create($data);

    /**
     * Returns elements that matches the query
     *
     * @param mixed $query
     * @return array
     */
    public function query($query);

    /**
     * Returns number of elements in collection
     *
     * @return int
     */
    public function count();

    /**
     * Sets resource for current data source
     *
     * @param string $resource
     * @return $this
     */
    public function setResource($resource);

    /**
     * Returns the selected resource
     *
     * @return string
     */
    public function getResource();

    /**
     * Returns underlying driver so you can do "things" at price of sacrificing abstraction
     *
     * @return mixed
     */
    public function getDriver();
}

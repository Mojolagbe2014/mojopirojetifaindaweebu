<?php

/** Interface that defines CRUD operations on contents */
interface ContentManipulator {
    /** method that check if a string variable is empty */
    public function notEmpty();
    
    /** Method that add something to a content
     */
    public function add();
    
    /** Method that  update content 
     */
    public function update();
    
    /** Method that deletes content
     */
    public function delete();
    
    /** Method that fetches content
     * @param string $column Column name of the data to be fetched
     * @param string $condition Additional condition e.g category_id > 9
     * @param string $sort column name to be used as sort parameter
     */
    public function fetch($column="*", $condition="", $sort="id");
}

<?php

class BlogManager {

    public function createBlog(BlogForm $blogForm) {
        if ($blogForm->validate()) {
            $blog = new Blog();
            $blog->attributes = $blogForm->attributes;
            $blog->id = null;
            if ($blog->save() === false) {
                $blogForm->addErrors($blog->getErrors());
                return false;
            }else{
                $blogForm->setId($blog->getId());
            }
        }


        return ($blogForm->hasErrors() === false);
    }

    public function updateBlog(BlogForm $blogForm) {
        if ($blogForm->validate()) {
            $blog = $this->loadBlogModelById($blogForm->id);

            $blog->attributes = $blogForm->attributes;
          
            if ($blog->save() === false) {
            
                $blogForm->addErrors($blog->getErrors());
                return false;
            }
        }
        return ($blogForm->hasErrors() === false);
    }

    public function deleteBlog(Blog $blog) {
        return $blog->delete(false);
    }

    /*
     * Load trip data into trip form.
     */

    public function loadFormModel($id, $with=null) {
        $blog = $this->loadBlogModelById($id, $with);
        $blogForm = new BlogForm();
        $blogForm->initModel($blog);

        return $blogForm;
    }

    public function loadBlogModelById($id, $with=null) {
        $blog = IBlog::model()->getById($id, $with);
        if ($blog === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $blog;
    }

}

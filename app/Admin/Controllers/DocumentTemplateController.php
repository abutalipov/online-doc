<?php

namespace App\Admin\Controllers;

use App\Models\DocumentTemplate;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DocumentTemplateController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Шаблоны документов';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DocumentTemplate());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('user_id','Владелец');
        $grid->column('path', 'Системный путь');
        $grid->column('name', 'Название');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(DocumentTemplate::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('user_id','Владелец');
        $show->field('path', 'Системный путь')->file();
        $show->field('name', 'Название');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DocumentTemplate);

        $form->display('id', __('ID'));
        $form->select('user_id','Владелец')->options(User::all()->pluck('name','id'));
        $form->file('path', 'Системный путь');
        $form->text('name', 'Название');
        return $form;
    }
}

<?php

namespace App\Admin\Controllers;

use App\Models\SecretKey;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SecretKeyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Секретные ключи';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SecretKey());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('user_id','Владелец');
        $grid->column('title', 'Заголовок');
        $grid->column('valid_from', 'Действителен с');
        $grid->column('valid_until', 'Действителен до');
        $grid->column('created_at', 'Создан');

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
        $show = new Show(SecretKey::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('user_id','Владелец');
        $show->field('title', 'Заголовок');
        $show->field('valid_from', 'Действителен с');
        $show->field('valid_until', 'Действителен до');
        $show->field('created_at', 'Создан');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SecretKey);

        $form->display('id', __('ID'));
        $form->select('user_id','Владелец')->options(User::all()->pluck('name','id'));
        $form->text('title', 'Заголовок');
        $form->text('data', 'Секретный ключ подписи');
        $form->datetime('valid_from', 'Действителен с');
        $form->datetime('valid_until', 'Действителен до');
        return $form;
    }
}

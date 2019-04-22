<?php

use Illuminate\Database\Seeder;

use App\ClassModel;
use App\Homework;
use App\HomeworkData;


class ClassAndHomeworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classTable = new ClassModel();
        $homeworkTable = new Homework();
        $homeworkDataTable = new HomeworkData();

        $classTable->insert([
            'users_id'  => 1,
            'name'      => '现代软件工程概论01'
        ]);

        $homeworkTable->insert([
            'classes_id'    => 1,
            'users_id'      => 1,
            'data'          => json_encode([
                [
                    'problem'   => '吴东是谁?',
                    'type'      => 'xz',
                    'reason'    => 'A',
                    'A'         => '现代软件工程之父',
                    'B'         => '未来软件工程之父',
                    'C'         => '传统软件工程之父',
                    'D'         => '古代软件工程之父'
                ],
                [
                    'problem'   => '吴东牛逼吗?',
                    'type'      => 'pd',
                    'reason'    => 'YES'
                ]
            ])
        ]);

        $homeworkDataTable->insert([
            'classes_id'    => 1,
            'users_id'      => 1,
            'data'          => json_encode([
                [
                    'problem'   => '吴东是谁?',
                    'type'      => 'xz',
                    'answer'    => 'B',
                    'A'         => '现代软件工程之父',
                    'B'         => '未来软件工程之父',
                    'C'         => '传统软件工程之父',
                    'D'         => '古代软件工程之父'
                ],
                [
                    'problem'   => '吴东牛逼吗?',
                    'type'      => 'pd',
                    'answer'    => 'NO'
                ]
            ])
        ]);
    }
}

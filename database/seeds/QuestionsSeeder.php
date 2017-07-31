<?php

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;


class QuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('questions')->delete();
        DB::table('answers')->delete();

        $answers = [
            [
                'answer_text' => 'Ответ 1',
                'is_right' => false
            ],
            [
                'answer_text' => 'Ответ 2',
                'is_right' => true
            ],
            [
                'answer_text' => 'Ответ 3',
                'is_right' => false
            ],
            [
                'answer_text' => 'Ответ 4',
                'is_right' => false
            ],
        ];

        $question = Question::create([
            'question_text' => 'Первый вопрос'
        ]);

        foreach ($answers as $answer)
        {
            $question->answers()->create($answer);
        }

        $question = Question::create([
            'question_text' => 'Второй вопрос'
        ]);

        foreach ($answers as $answer)
        {
            $question->answers()->create($answer);
        }
        $question = Question::create([
            'question_text' => 'Третий вопрос'
        ]);

        foreach ($answers as $answer)
        {
            $question->answers()->create($answer);
        }
        $question = Question::create([
            'question_text' => 'Четвертый вопрос'
        ]);

        foreach ($answers as $answer)
        {
            $question->answers()->create($answer);
        }
        $question = Question::create([
            'question_text' => 'Пятый вопрос'
        ]);

        foreach ($answers as $answer)
        {
            $question->answers()->create($answer);
        }
        $question = Question::create([
            'question_text' => 'Шестой вопрос'
        ]);

        foreach ($answers as $answer)
        {
            $question->answers()->create($answer);
        }

    }
}

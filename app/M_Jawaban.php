<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Jawaban extends Model
{
    //
    protected $table = 'tbl_jawaban';
    protected $primaryKey = 'id_jawaban';
    protected $fillable = ['id_jawaban', 'id_peserta', 'id_skor', 'id_soal', 'status_jawaban', 'jawaban'];

}

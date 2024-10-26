<?Php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [

        'priority',
        'title',
        'body',
        'user',
        'isAnswer',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'departmentID');
    }

    public function departmentSub()
    {
        return $this->belongsTo(DepartmentSub::class, 'departmentSubID');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Reply()
    {
        return $this->hasMany(Reply::class);
    }



    public function parentTicket()
    {
        return $this->belongsTo(Ticket::class, 'parent');
    }
}

<?php
/**
 * Модель для работы с АПИ Мегаплана
 */

namespace Surrexi\Megaplan;

class Megaplan
{
    protected $req;
    protected $params = [];
    protected $host = null;
    protected $login = null;
    protected $password = null;
    protected $https = true;

    public function __construct()
    {
        $this->host = config('megaplan.host');
        $this->login = config('megaplan.login');
        $this->password = config('megaplan.password');
        $this->https = config('megaplan.https');

        // Авторизуемся в Мегаплане
        $this->req = (new Client($this->host))->useHttps($this->https)->auth($this->login, $this->password);
    }

    /**
     * Создание клиента
     *
     * @param $type - приниает human или company
     * @param null $attaches
     * @param null $fields - доп поля array('название поля' => 'значение')
     * @return mixed|string
     * @throws \Exception
     */
    public function addContractor($type, array $fields = null, $attaches = null, $ignore = true)
    {
        $this->params = [];
        $this->params['Model[TypePerson]'] = $type;
        if (!is_null($fields)) {
            foreach ($fields as $key => $val) {
                $this->params["Model[$key]"] = $val;
            }
        }
        $this->params['Model[Attaches][Add]'] = $attaches;
        $this->params['IgnoreRequiredFields'] = $ignore;

        return $this->req->post('/BumsCrmApiV01/Contractor/save.api', $this->params);
    }

    /**
     * Создание комментария
     *
     * @param $subjectType - приниает task (задача), project (проект), contractor (клиент), deal (сделка), discuss (обсуждение)
     * @param $id - ID комментируемого объекта
     * @param $text - Текст комментария
     * @param $work - Кол-во потраченных минут, которое приплюсуется к комментируемому объекту (задача или проект)
     * @return mixed|string
     * @throws \Exception
     */
    public function commentCreate($subjectType, $id, $text, $attaches = null)
    {
        $this->params = [];
        $this->params['SubjectType'] = $subjectType;
        $this->params['SubjectId'] = $id;
        $this->params['Model[Text]'] = $text;
        $this->params['Model[Attaches]'] = $attaches;

        return $this->req->post('/BumsCommonApiV01/Comment/create.api', $this->params);
    }
}

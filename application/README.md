## Iniciando o projeto

Com [docker](https://docs.docker.com/engine/install/) instalado, rode o comando para subir os containers com o php-apache e o mysql:
```bash
$ docker-compose up -d
```
Ao fim processo, se tudo estiver corrido corretamente, o container com estará rodando na porta 8080 já com a base conectada. Acesse http://localhost:8080 para ver a tela de boas vindas do laravel.


## Estrutura de pastas
Todo o código contido em `./application` será servido pelo servidor apache rodando no container.

As principais pastas do laravel são:

- **/public** : Entry-point da aplicação. Se observar no Dockerfile, editamos o arquivo de configurações do apache para usar esta pasta como ponto de entrada ao acessar a url.
- **/resources** :  Onde ficam os arquivos estáticos, imagens, css, js, templates (e.g. html ou blade).
- **/routes** : Onde ficam as rotas.
- **/tests** : Óbvio
- **/database** : Onde ficam as migrations, factories e seeders.
- **/app/Http**: Onde ficam controllers e middlewares.
- **/app/Models**: Onde ficam os models.

## Primeiros passos

O laravel conta com o artisan, um `cli` que deixa bem fácil gerar as classes básicas como migrations, controllers e models.
A forma mais fácil de executar um comando do artisan sem precisar instalar o php na sua maquina host é acessar o container com `$ docker exec -it gazin-back bash`, gazin-back é o nome dado ao container, pode ser alterado no [docker-compose.yml]() na raiz do projeto.

### Criando a primeira migration

```bash
# acesse o container
$ docker exec -it gazin-back bash
#rode o comando para criar o model com -m para criar migration
$ php artisan make:model Developer -m
```
O comando irá criar um model com o nome `Developer` dentro de `app/Models` e uma migration com o nome `<data_hora>_create_developers_table` dentro de `database/migrations`

### Criando uma tabela

Com a migration e o model criados, vamos adicionar os campos à migration para então criarmos a tabela developer.

Por padrão, todas tabelas tem um campo id. Isto pode ser alterado, mas  padrao de criação de nomes do laravel, facilitará bastante criar relações entre as tabelas utilizando o `Query Builder` do laravel.
Observe que o nome do model é no singular e a tabela no plural.


As migrations tem dois métodos publicos que serão chamados na hora de rodar a migração ou rollback respectivamente: `up` e `down`

```PHP
//chamada ao migrar
public function up(){

    Schema::create('developers', function (Blueprint $table) {
        $table->id();
        $table->string("nome");
        $table->enum("sexo", ["M", "F", "O"]);
        $table->integer("idade");
        $table->string("hobby");
        $table->date("datanascimento");
        $table->timestamps();
    });
}
```
```PHP
//chamada ao dar rollback
public function down(){
    Schema::dropIfExists('developers');
}
```
https://laravel.com/docs/8.x/migrations

### Migrando

As migrations armazenam um histórico das modificações das tabelas. Elas são rodadas de acordo com o nome, em ordem decrescente.
Quando geradas pelo artisan ficam com o prefixo da data e hora, mas podem ser alteradas como quiser, respeitando a ordem do nome.

```bash
# executar migrations
$ php artisan migrate
```

Executando o comando será gerada a tabela `developers` com os campos da migration, e, será adicionada a versão à tabela de controle `migrations` criada pelo laravel na primeira "migrada".

### Primeira rota

O comando irá criar o controller dentro da pasta `app/Http/Controllers`.

```bash
# criar controller
$ php artisan make:controller Developers
```

Com o controller criado podemos adicionar a primeira rota em `app/routes/api.php`, apontando para um método `createDeveloper` do controller, que ainda será criado:

```PHP
use App\Http\Controllers\Developers;

Route::post('/developers', [Developers::class, 'createDeveloper']);

```

### Primeira action

```PHP

    private $rules = [];
    private $messages = [];

    public function createDeveloper(Request $request)
    {

        $requestData = $request->all();

        //validar entrada de acordo com as regras
        $validator = Validator::make($requestData, $this->rules, $this->messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        try {
            $developer = new Developer();
            $developer->fill($requestData);

            //Será adicionada ao Model mais abaixo para calcular a idade de acordo com a data de nascimento
            $developer->setIdadeAttribute();

            $developer->save();

            return response()->json($developer, 201);
        } catch (\Exception $e) {
            $code =  $e->getCode();
            return response()->json(["error" => "Houve um erro interno. ERRNO: $code"], 500);
        }
    }
```

Utilizando a casse Validator pra criar as validações, passamos duas arrays: `rules` e `messages`.

Se a validação passar, então será criado um novo `developer` com os dados do request, mas para funcionar o método fill do model, é necessário informar explicitamente no `Model` que ele deve aceitar um array, caso contrário precisariamos adicionar campo por campo.

``` PHP
    private $rules = [
        'nome' => 'required',
        'sexo' => 'required|in:M,F,O',
        'hobby' => 'required',
        'datanascimento' => 'required|date|before:today'
    ];

    private $messages = [
        'nome.required' => "O nome é obrigatório.",
        'sexo.required' => "O sexo é obrigatório.",
        'sexo.in' => "O valor informado é inválido.",
        'hobby.required' => "O hobby é obrigatório.",
        'datanascimento.required' => "A data de nascimento é obrigatória.",
        'datanascimento.date' => "Formato de data inválido.",
        'datanascimento.before' => "A data de nascimento precisa ser inferior à hoje."
    ];
```


https://laravel.com/docs/8.x/validation
```PHP
//@class app/Models/Developer

protected $fillable = [
    "datanascimento",
    "sexo",
    "nome",
    "hobby"
];
```

Observe que não existe o campo `idade` no array `fillable`, pois calcularemos a idade de acordo com a data de nascimento informada, utilizando o [Carbon](https://carbon.nesbot.com/docs/), que vem por padão no laravel

```PHP
public function setIdadeAttribute()
{
    $this->attributes['idade'] =  \Carbon\Carbon::parse($this->attributes['datanascimento'])->age;
}
```

### Pronto!
Pronto, agora é só mandar bala!

Faça um POST na rota `http://localhost:8080/api/developers` e veja a magica acontecer!


```JSON
{
    "nome": "Edson Junior",
    "sexo": "M",
    "hobby": "Musica",
    "datanascimento": "1995-02-23"
}
```



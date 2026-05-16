# Nota Operacional para AGENTS/CODEX

Este projeto usa como workspace principal apenas a copia local em `/Users/ranielejf/Projetos/answer`.

## Regras sempre ativas

- nao usar a copia antiga no iCloud como pasta ativa de desenvolvimento;
- tratar qualquer pasta antiga no iCloud apenas como backup historico;
- considerar `doc/condex` como base canonica de produto e regras funcionais durante a migracao documental;
- manter a conversa com o usuario em portugues, mas todo desenvolvimento deve ser em ingles: telas, botoes, labels, mensagens de erro, mensagens de sessao, validacoes, testes/asserts de texto, comentarios novos em codigo e documentacao funcional nova nao devem introduzir texto em portugues;
- manter `.gitignore` cobrindo artefatos locais como `vendor`, `node_modules`, `storage/logs`, `storage/framework/views`, builds e caches;
- separar limpeza estrutural, codigo funcional e documentacao em commits distintos quando houver commits multiplos.

## Modo enxuto para tarefas simples

Para ajustes pequenos e localizados, como mudancas de Blade, Tailwind, texto, coluna, alinhamento visual ou correcao pontual sem impacto aparente em infraestrutura:

- nao reler `docs/*` por padrao;
- nao fazer auditoria ampla da arvore;
- nao validar Git/VPS antes de editar, a menos que o usuario peca `git`, `commit`, `push`, `deploy`, `vps` ou equivalente;
- buscar primeiro no arquivo ou area mais provavel, evitando varredura ampla do projeto;
- usar MCP/Laravel Boost se estiver disponivel, mas nao bloquear a tarefa simples se nao estiver.

### Regra de economia de contexto

Quando a tarefa parecer simples ou localizada, operar em modo economico de forma agressiva:

- abrir primeiro apenas 1 arquivo provavel ou 1 trecho curto de log, nunca varios artefatos em paralelo por padrao;
- evitar respostas longas de tools quando um trecho curto resolver, especialmente stack traces completos, dumps extensos e leituras amplas de arquivo;
- evitar releitura do mesmo arquivo em blocos grandes se a causa ja estiver visivel;
- nao usar paralelismo de tools por padrao em tarefas simples; paralelizar apenas quando houver ganho claro sem aumentar muito o contexto;
- corrigir primeiro e ampliar a investigacao apenas se a primeira hipotese falhar;
- preferir validacao minima: 1 teste relacionado, 1 rota relacionada ou 1 comando objetivo;
- so rodar suite completa, build, deploy ou verificacoes amplas se o usuario pedir ou se a mudanca realmente justificar;
- se a tarefa crescer alem do esperado, avisar o usuario antes de abrir escopo de investigacao.

### Heuristica pratica

Classificar por padrao como tarefa simples:

- ajuste visual ou de texto;
- erro localizado com rota, controller, blade, componente ou query claramente identificavel;
- correcao pequena em teste existente;
- mudanca pontual sem indicio de drift operacional, infraestrutura ou multiplos modulos.

Classificar como tarefa mais complexa apenas quando houver sinal concreto, por exemplo:

- erro sem origem identificavel apos a primeira leitura curta;
- necessidade real de cruzar varios modulos, logs, filas, banco e infraestrutura;
- suspeita de drift entre local, Git e VPS;
- pedido explicito de auditoria, diagnostico amplo, deploy ou investigacao de producao.

## Validacoes obrigatorias apenas quando necessario

Executar `pwd`, `git rev-parse --show-toplevel`, `git remote -v` e `git status --branch` apenas quando:

- a tarefa envolver commit, push, branch, rebase, revert ou deploy;
- houver indicio de que o workspace atual pode estar errado;
- o usuario pedir diagnostico operacional ou retomada completa de contexto.

## Retomada de contexto

Usar `docs/README.md` como ponto de entrada e fazer auditoria rapida da arvore apenas quando:

- a sessao estiver retomando trabalho antigo sem contexto recente;
- a tarefa tocar producao, deploy, integracoes ou regras operacionais;
- houver duvida real sobre drift entre local, Git e VPS.

## Prioridade de leitura ao retomar

1. `docs/operations/current-state.md`
2. `docs/operations/production-alignment.md`
3. `docs/runbooks/surgical-update-method.md`
4. `docs/runbooks/laravel-boost-router.md`
5. `docs/devlogs/`
6. `doc/condex/`

## Regras operacionais atuais

- o monolito Laravel e o escopo principal;
- N8N e operacionalmente relevante, mas fica fora do deploy da aplicacao;
- producao atual roda em `vps-hetzner`, path `/opt/apps/pharfind`;
- o deploy oficial deve usar Git + `scripts/deploy-hetzner.sh`;
- endpoint publico de deploy e considerado legado e nao deve ser mantido;
- qualquer alteracao que toque producao deve considerar backup, drift Git remoto e saneamento da VPS.

## MCP e roteador Laravel

- usar o `laravel-boost-router` compartilhado em `/Users/ranielejf/Projetos/laravel-boost-router` para clientes MCP locais sempre que houver mais de um projeto Laravel ativo no ambiente;
- preferir chamadas `router-call` com `target` explicito quando o roteador estiver em uso;
- usar tools MCP prefixadas por projeto, como `answer_*`, apenas como compatibilidade ou diagnostico;
- quando um novo projeto Laravel entrar no conjunto roteado, adicionar uma entrada propria no objeto `projects` de `/Users/ranielejf/Projetos/laravel-boost-router/src/server.js`.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.7
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/socialite (SOCIALITE) - v5
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v3

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v3 rules ===

## Tailwind 3

- Always use Tailwind CSS v3 - verify you're using only classes supported by this version.
</laravel-boost-guidelines>

## Legacy Reference Rule
- The project `/Users/ranielejf/Projetos/answer` is the legacy reference source for discovery and consultation only.
- Use it to understand business rules, workflows, and data contracts while designing the new admin portal in `answer-admin`.
- Do not treat legacy implementation details as mandatory; prefer clean implementation in `answer-admin` that preserves functional intent.

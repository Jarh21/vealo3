<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\ConfiguracionController;
class ImportarCxpConNotacredito extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sincronizar:cxp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cuando el vealo esta en dos lugares distintos y uno de ellos debe tener la informcion del remoto, este comado migra las tablas necesarias al lugar de trabajo donde se tienen que supervisar dichos datos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sincronizarCxp = new ConfiguracionController();
        $sincronizarCxp->sincronizarVealoLocal_VealoRemoto();
    }
}

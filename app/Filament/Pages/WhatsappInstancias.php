<?php

namespace App\Filament\Pages;

use App\Models\EvolutionInstance;
use App\Services\EvolutionService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\On;

class WhatsappInstancias extends Page
{
    protected string $view = 'filament.pages.whatsapp-instancias';

    protected static string|\UnitEnum|null $navigationGroup = 'Comunicação';

    protected static ?string $navigationLabel = 'WhatsApp';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $title = 'WhatsApp — Instâncias';

    protected static ?int $navigationSort = 2;

    // ─── Form de criar/editar ─────────────────────────────────────────
    public bool   $showForm    = false;
    public ?int   $editingId   = null;
    public string $nome        = '';
    public string $instancia   = '';
    public string $url_base    = '';
    public string $api_key     = '';
    public bool   $ativo       = true;
    public bool   $padrao      = false;

    // ─── QR Code modal ───────────────────────────────────────────────
    public ?string $qrCodeBase64   = null;
    public ?string $qrInstanciaNome = null;

    // ─── Teste rápido ────────────────────────────────────────────────
    public ?int   $testInstanceId = null;
    public string $testPhone      = '';

    public static function getNavigationBadge(): ?string
    {
        $connected = EvolutionInstance::where('status_conexao', 'open')->count();
        return $connected > 0 ? (string) $connected : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    // ─── CRUD ─────────────────────────────────────────────────────────

    public function novaInstancia(): void
    {
        $this->reset(['editingId', 'nome', 'instancia', 'url_base', 'api_key', 'padrao']);
        $this->ativo    = true;
        $this->showForm = true;
    }

    public function editarInstancia(int $id): void
    {
        $inst = EvolutionInstance::findOrFail($id);
        $this->editingId  = $id;
        $this->nome       = $inst->nome;
        $this->instancia  = $inst->instancia;
        $this->url_base   = $inst->url_base;
        $this->api_key    = $inst->api_key;
        $this->ativo      = $inst->ativo;
        $this->padrao     = $inst->padrao;
        $this->showForm   = true;
    }

    public function salvar(): void
    {
        $this->validate([
            'nome'      => 'required|string|max:80',
            'instancia' => 'required|string|max:80',
            'url_base'  => 'required|url',
            'api_key'   => 'required|string',
        ]);

        // Remover padrão das outras se esta será padrão
        if ($this->padrao) {
            EvolutionInstance::where('id', '!=', $this->editingId ?? 0)->update(['padrao' => false]);
        }

        $dados = [
            'nome'      => $this->nome,
            'instancia' => $this->instancia,
            'url_base'  => rtrim($this->url_base, '/'),
            'api_key'   => $this->api_key,
            'ativo'     => $this->ativo,
            'padrao'    => $this->padrao,
        ];

        if ($this->editingId) {
            EvolutionInstance::findOrFail($this->editingId)->update($dados);
            Notification::make()->title('Instância atualizada!')->success()->send();
        } else {
            EvolutionInstance::create($dados);
            Notification::make()->title('Instância criada!')->success()->send();
        }

        $this->showForm = false;
    }

    public function excluir(int $id): void
    {
        EvolutionInstance::findOrFail($id)->delete();
        Notification::make()->title('Instância excluída.')->warning()->send();
    }

    // ─── Conexão ─────────────────────────────────────────────────────

    public function testarConexao(int $id): void
    {
        $inst = EvolutionInstance::findOrFail($id);
        $ok   = $inst->testarConexao();

        if ($ok) {
            Notification::make()->title("🟢 {$inst->nome} — Conectado!")->success()->send();
        } else {
            Notification::make()->title("🔴 {$inst->nome} — Sem conexão.")->danger()->send();
        }
    }

    public function verQrCode(int $id): void
    {
        $inst                 = EvolutionInstance::findOrFail($id);
        $base64               = $inst->getQrCode();
        $this->qrInstanciaNome = $inst->nome;
        $this->qrCodeBase64   = $base64
            ? (str_starts_with($base64, 'data:') ? $base64 : "data:image/png;base64,{$base64}")
            : null;
    }

    public function fecharQr(): void
    {
        $this->qrCodeBase64   = null;
        $this->qrInstanciaNome = null;
    }

    // ─── Envio de Teste ───────────────────────────────────────────────

    public function enviarTeste(): void
    {
        $this->validate(['testPhone' => 'required|min:10']);

        $inst = EvolutionInstance::findOrFail($this->testInstanceId);
        $r    = $inst->sendText(
            $this->testPhone,
            "🧪 *Teste Elite Repasse*\n\nMensagem de teste enviada pelo painel admin.\n\n_Instância: {$inst->nome}_"
        );

        if ($r['success']) {
            Notification::make()->title('✅ Mensagem enviada com sucesso!')->success()->send();
        } else {
            Notification::make()->title('❌ Falha: ' . ($r['error'] ?? 'Verifique os dados.'))->danger()->send();
        }

        $this->testInstanceId = null;
        $this->testPhone      = '';
    }

    public function getInstanciasProperty()
    {
        return EvolutionInstance::orderByDesc('padrao')->orderBy('nome')->get();
    }
}

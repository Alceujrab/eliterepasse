<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-[80px]">
        
        <!-- Logo Left -->
        <div class="flex-shrink-0 flex items-center">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-3 group">
                <img src="{{ asset('build/assets/logo.png') }}" class="h-10 transition transform group-hover:scale-105" alt="Elite Repasse" onerror="this.src='https://placehold.co/200x50/1f5a7c/white?text=Elite+Repasse'">
                <span class="text-gray-300 font-light text-3xl hidden md:block">|</span>
                <span class="text-gray-800 font-black tracking-tight text-xl hidden md:block">Portal do Lojista</span>
            </a>
        </div>

        <!-- Center Search (Disabled functionality, just visual mirror) -->
        <div class="flex-1 max-w-2xl px-12 hidden lg:block">
            <div class="relative">
                <input type="text" class="w-full bg-[#f8fafc] border border-gray-200 text-gray-900 rounded-full py-3.5 pl-12 pr-4 focus:ring-primary focus:border-primary shadow-inner text-sm font-medium transition focus:bg-white" placeholder="Pesquise por marca, modelo ou ano">
                <div class="absolute left-4 top-4 text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Right Menu -->
        <div class="flex items-center space-x-8">
            
            <!-- Quick Favorite -->
            <a href="{{ route('favoritos') }}" class="text-gray-400 hover:text-red-500 transition hidden sm:block relative cursor-pointer" title="Meus Favoritos" wire:navigate>
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </a>

            <!-- Mega Dropdown -->
            <x-dropdown align="right" width="w-[640px]" contentClasses="py-0 bg-white rounded-xl">
                <x-slot name="trigger">
                    <button class="flex items-center space-x-3 hover:bg-gray-50 p-2 pr-3 rounded-lg transition border border-transparent hover:border-gray-200 cursor-pointer">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg shadow-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="text-left hidden sm:flex flex-col items-start leading-tight">
                            <span class="text-[15px] font-bold text-gray-800 tracking-tight">
                                {{ explode(' ', auth()->user()->name)[0] }} Lojista
                            </span>
                            <span class="text-xs text-gray-500 font-medium flex items-center">
                                Minha Conta <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                            </span>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="p-8">
                        <!-- Profile Header -->
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h4 class="text-3xl font-black text-gray-900 tracking-tight">Olá, {{ explode(' ', auth()->user()->name)[0] }}!</h4>
                                <p class="text-[15px] text-gray-500 font-bold uppercase tracking-wider mt-1.5">{{ auth()->user()->companies()->first()->razao_social ?? 'Empresa não vinculada' }}</p>
                            </div>
                            <div class="bg-[#f3f4f6] border border-gray-200 text-gray-700 px-5 py-2.5 rounded-full text-sm font-bold flex items-center gap-2 tracking-wide shadow-sm">
                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                Cliente Parceiro
                            </div>
                        </div>

                        <div class="w-full h-px bg-gray-200 mb-8"></div>

                        <!-- Menu Grid -->
                        <div class="grid grid-cols-2 gap-x-12 gap-y-3">
                            <!-- Col 1 -->
                            <div class="space-y-1">
                                <a href="{{ route('pedidos') }}" class="flex items-center gap-4 group p-3 hover:bg-[#f8fafc] rounded-xl transition" wire:navigate>
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span class="text-[16px] font-semibold text-gray-700 group-hover:text-primary transition-colors">Meus Pedidos</span>
                                </a>

                                <a href="{{ route('financeiro') }}" class="flex items-center gap-4 group p-3 hover:bg-[#f8fafc] rounded-xl transition" wire:navigate>
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span class="text-[16px] font-semibold text-gray-700 group-hover:text-primary transition-colors">Financeiro</span>
                                </a>

                                <a href="{{ route('meus-documentos') }}" class="flex items-center gap-4 group p-3 hover:bg-[#f8fafc] rounded-xl transition" wire:navigate>
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                    <span class="text-[16px] font-semibold text-gray-700 group-hover:text-primary transition-colors">Meus Documentos</span>
                                </a>

                                <a href="#" class="flex items-center gap-4 group p-3 hover:bg-[#f8fafc] rounded-xl transition">
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <span class="text-[16px] font-semibold text-gray-700 group-hover:text-primary transition-colors">Selecionar Empresa</span>
                                </a>
                                
                                <button wire:click="logout" class="w-full text-left flex items-center gap-4 group p-3 hover:bg-red-50 rounded-xl transition mt-6">
                                    <svg class="w-6 h-6 text-red-500 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    <span class="text-[16px] font-bold text-red-600 group-hover:text-red-700 transition">Sair do sistema</span>
                                </button>
                            </div>

                            <!-- Col 2 -->
                            <div class="space-y-1">
                                <a href="#" class="flex items-center gap-4 group p-3 hover:bg-[#f8fafc] rounded-xl transition" wire:navigate>
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    <span class="text-[16px] font-semibold text-gray-700 group-hover:text-primary transition-colors">Favoritos</span>
                                </a>

                                <a href="{{ route('suporte') }}" class="flex items-center gap-4 group p-3 hover:bg-[#f8fafc] rounded-xl transition" wire:navigate>
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    <span class="text-[16px] font-semibold text-gray-700 group-hover:text-primary transition-colors">Meus Chamados</span>
                                </a>

                                <a href="{{ route('documentos-elite') }}" class="flex items-center gap-4 group p-3 hover:bg-[#f8fafc] rounded-xl transition" wire:navigate>
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span class="text-[16px] font-semibold text-gray-700 group-hover:text-primary transition-colors">Documentos Elite</span>
                                </a>

                                <a href="{{ route('profile') }}" class="flex items-center gap-4 group p-3 hover:bg-[#f8fafc] rounded-xl transition mt-6" wire:navigate>
                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="text-[16px] font-semibold text-gray-700 group-hover:text-primary transition-colors">Minha conta</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>

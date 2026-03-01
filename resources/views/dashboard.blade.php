<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-gray-800 leading-tight tracking-tight">
                {{ __('Main Dashboard') }}
            </h2>
            @if($user->role === 'admin')
            <span class="bg-indigo-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase shadow-lg">
                Platform Administrator
            </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if($user->role === 'admin')
            <div class="bg-white border-2 border-indigo-500 rounded-3xl shadow-xl overflow-hidden">
                <div class="bg-indigo-500 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="font-black text-xs uppercase tracking-widest flex items-center">
                        Admin Overview
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 divide-x divide-gray-100">
                    <div class="p-6 text-center">
                        <p class="text-[10px] text-gray-400 font-black uppercase">Total Users</p>
                        <p class="text-3xl font-black text-indigo-600">{{ $admin_Stats['total_users'] ?? 0 }}</p>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-[10px] text-gray-400 font-black uppercase">Colocations</p>
                        <p class="text-3xl font-black text-indigo-600">{{ $admin_Stats['total_colocations'] ?? 0 }}</p>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-[10px] text-red-400 font-black uppercase">Banned</p>
                        <p class="text-3xl font-black text-red-600">{{ $admin_Stats['banned_users'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                @if(!$activeColocation)

                <div class="max-w-2xl mx-auto text-center py-10">
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Join a Colocation</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        <form action="{{ route('colocations.store') }}" method="POST" class="p-6 bg-emerald-50 rounded-2xl">
                            @csrf
                            <input type="text" name="name" placeholder="House Name" class="w-full rounded-xl border-gray-200 mb-4" required>
                            <button class="w-full bg-emerald-600 text-white font-black py-3 rounded-xl hover:bg-emerald-700">CREATE</button>
                        </form>
                        <form action="{{ route('colocations.join') }}" method="POST" class="p-6 bg-sky-50 rounded-2xl">
                            @csrf
                            <input type="text" name="token" placeholder="Invite Token" class="w-full rounded-xl border-gray-200 mb-4" required>
                            <button class="w-full bg-sky-600 text-white font-black py-3 rounded-xl hover:bg-sky-700">JOIN</button>
                        </form>
                    </div>
                </div>
                @else
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <span class="text-indigo-400 text-[10px] font-black uppercase tracking-widest">Active Colocation</span>
                        <h2 class="text-5xl font-black text-indigo-950 tracking-tighter">{{ $activeColocation->name }}</h2>
                    </div>


                    @if($activeColocation)
                    @if($activeColocation->pivot->role === 'owner')
                    <form action="{{ route('colocations.cancel', $activeColocation->id) }}" method="POST" onsubmit="return confirm('Cancel house and exit everyone?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="bg-rose-100 text-rose-700 px-4 py-2 rounded-xl text-xs font-black uppercase">
                            Cancel Colocation
                        </button>
                    </form>
                    @else
                    <form action="{{ route('colocations.quit', $activeColocation->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to quit?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="bg-amber-100 text-amber-700 px-4 py-2 rounded-xl text-xs font-black uppercase">
                            Quit Colocation
                        </button>
                    </form>
                    @endif
                    @endif


                    @if($activeColocation->pivot && $activeColocation->pivot->role === 'owner')
                    <div class="bg-indigo-50 p-6 rounded-3xl border-2 border-white shadow-xl text-center min-w-[240px]">
                        <p class="text-[10px] text-gray-400 font-black mb-2 tracking-widest">INVITATION TOKEN</p>
                        <span class="text-2xl font-mono font-black text-indigo-600 px-5 py-2 rounded-xl block bg-white border border-indigo-100 select-all">
                            {{ $activeColocation->invite_token }}
                        </span>
                    </div> @endif
                </div>
                @endif
            </div>


            <!-- add a category  -->
            @if($activeColocation)
            <div class="bg-white p-6 rounded-3xl shadow-sm border-2 border-indigo-50 mt-4">
                <h3 class="font-black text-gray-800 text-xs mb-4 uppercase tracking-widest">Create Private Category</h3>

                <form action="{{ route('categories.store') }}" method="POST" class="flex gap-4">
                    @csrf
                    @if($activeColocation)
                    <input type="hidden" name="colocation_id" value="{{ $activeColocation->id }}">
                    @endif

                    <input type="text" name="name" placeholder="Category Name"
                        class="flex-1 rounded-2xl border-gray-200 " required>

                    <button type="submit" class="bg-emerald-600 text-white font-black px-6 py-3 rounded-2xl hover:bg-emerald-700 transition">
                        Add
                    </button>
                </form>
            </div>
            @endif


            @if($activeColocation)
            <div class="bg-white p-6 rounded-3xl shadow-sm border-2 border-indigo-50">
                <h3 class="font-black text-gray-800 text-xs mb-4 uppercase tracking-widest">Add & Split Expense</h3>
                <form action="{{ route('expenses.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @csrf
                    <input type="hidden" name="colocation_id" value="{{ $activeColocation->id }}">
                    <input type="text" name="title" placeholder="Description" class="rounded-2xl border-gray-200" required>
                    <input type="number" name="amount" placeholder="Amount (DH)" class="rounded-2xl border-gray-200" required>
                    <select name="category_id" class="rounded-2xl border-gray-200">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="rounded-2xl border-gray-200">
                    <button type="submit" class="bg-indigo-600 text-white font-black py-3 rounded-2xl hover:bg-indigo-700 transition">SAVE & SPLIT</button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-3xl shadow-sm border-b-8 border-rose-500">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">You Owe</p>
                    <h4 class="text-4xl font-black text-rose-600 mt-1">{{ number_format($totalToPay, 2) }} DH</h4>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border-b-8 border-emerald-500">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Owed to You</p>
                    <h4 class="text-4xl font-black text-emerald-600 mt-1">{{ number_format($totalToCollect, 2) }} DH</h4>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border-b-8 border-amber-400">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Reputation</p>
                    <h4 class="text-4xl font-black text-amber-500 mt-1">⭐ {{ $user->reputation ?? 0 }}</h4>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-red-100">
                    <h3 class="font-black text-red-900 text-xs mb-4 uppercase tracking-widest flex items-center">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                        You Owe (Debt)
                    </h3>
                    <div class="space-y-3">
                        @forelse($debtsIOwe as $debt)
                        <div class="flex items-center justify-between p-4 {{ $debt->is_paid ? 'bg-gray-50 border-gray-100' : 'bg-red-50 border-red-100' }} rounded-2xl border">
                            <div>
                                <p class="text-sm font-black {{ $debt->is_paid ? 'text-gray-500' : 'text-red-900' }}">
                                    Pay to : <a class="underline">{{ $debt->receiver->name }} </a>
                                </p>
                                <p class="text-xs {{ $debt->is_paid ? 'text-gray-400' : 'text-red-600' }} font-bold">
                                    {{ number_format($debt->amount, 2) }} DH
                                </p>
                            </div>
                            @if(!$debt->is_paid)
                            <form action="{{ route('payments.markAsPaid', $debt->id) }}" method="POST">
                                @csrf
                                <button class="bg-red-500 text-white text-[10px] font-black px-4 py-2 rounded-xl hover:bg-red-600 transition uppercase">
                                    Mark Paid
                                </button>
                            </form>
                            @else
                            <span class="text-gray-500 font-black text-xs uppercase">Paid</span>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-400 text-sm italic">No debts to pay.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-emerald-100">
                    <h3 class="font-black text-emerald-900 text-xs mb-4 uppercase tracking-widest flex items-center">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                        They Owe You (Collect)
                    </h3>
                    <div class="space-y-3">
                        @forelse($debtsToMe as $debt)
                        <div class="flex items-center justify-between p-4 {{ $debt->is_paid ? 'bg-gray-50 border-gray-100' : 'bg-emerald-50 border-emerald-100' }} rounded-2xl border">
                            <div>
                                <p class="text-sm font-black {{ $debt->is_paid ? 'text-gray-500' : 'text-emerald-900' }}">
                                    <a class="underline">{{ $debt->sender->name }}</a> : should pay you
                                </p>
                                <p class="text-xs {{ $debt->is_paid ? 'text-gray-400' : 'text-emerald-600' }} font-bold">
                                    {{ number_format($debt->amount, 2) }} DH
                                </p>
                            </div>
                            <span class="{{ $debt->is_paid ? 'text-gray-500' : 'text-emerald-500' }} font-black text-xs uppercase">
                                {{ $debt->is_paid ? 'Paid' : 'Pending' }}
                            </span>
                        </div>
                        @empty
                        <p class="text-gray-400 text-sm italic">No debts to collect.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="font-black text-gray-800 uppercase text-xs tracking-widest">Recent Expenses</h3>
                        </div>
                        <div class="p-6">
                            <table class="w-full text-left">
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($recentExpenses as $expense)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="py-4 font-bold text-gray-800">{{ $expense->title }}</td>
                                        <td class="py-4 text-sm text-gray-500">Paid by {{ $expense->payer->name }}</td>
                                        <td class="py-4 text-right font-black text-indigo-950">{{ number_format($expense->amount, 2) }} DH</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="font-black text-gray-800 text-xs mb-6 uppercase tracking-widest border-b pb-4">Roommates</h3>
                    @php
                    $currentMember = $activeColocation->members->where('id', auth()->id())->first();
                    $isHouseAdmin = $currentMember && $currentMember->pivot->role === 'admin';
                    @endphp
                    <div class="space-y-4">
                        @foreach($activeColocation->members->where('pivot.left_at', null) as $member)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900 leading-none">{{ $member->name }}</p>
                                    <span class="text-[9px] font-black uppercase text-indigo-400">
                                        {{ $member->pivot->role ?? 'member' }}
                                    </span>
                                </div>
                            </div>
                            @if($isHouseAdmin && $member->id !== auth()->id())
                            <form action="{{ route('colocations.members.kick', [$activeColocation->id, $member->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Remove member?')" class="text-red-400 hover:text-red-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
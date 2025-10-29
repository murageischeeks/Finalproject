<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md 
                font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 
                focus:bg-indigo-700 active:bg-indigo-700 focus:outline-none transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>

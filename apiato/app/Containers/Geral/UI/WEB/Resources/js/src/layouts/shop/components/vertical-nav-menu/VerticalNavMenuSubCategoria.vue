<template>
	<div>
		<ul :v-show="categorias && categorias.length > 0 ? true : false">
			<li
				v-for="item in categorias"
				:key="item.id"
				class="items-center mb-2"
			>
				<span
					class="ml-2 cursor-pointer"
					:class="{'text-primary': isSubRefined(item.nome, categoria)}"
					@click="refineCategoria(item.nome)"
				>{{ item.nome }}</span>
			</li>
		</ul>
	</div>
</template>
<script>

export default {
	name: "v-nav-menu-sub-categoria",
	props: {
		categorias: { type: Array, required: false },
		categoria: { type: String, required: true },
		refineSubCategoria: {
			type: Function,
			required: true,
		},
		isRefined: {
			type: Function,
			required: true,
		},
		isSubRefined: {
			type: Function,
			required: true,
		},
	},
	methods: {
		refineCategoria(nome) {
			this.refineSubCategoria(nome, this.categoria, this);
		},
	}
};
</script>

<template>
	<!--<router-link
		tag="img"
		class="cursor-pointer"
		src="/images/logo/zara.png"
		to="/"
		@click="onClick"
	>
	</router-link>-->
	<img src="/images/logo/zara.png" @click="onClick"/>
</template>
<script>
export default {
	name: "logo",
	props: {
		onClick: {
			type: Function,
			required: true,
		},
	},
};
</script>

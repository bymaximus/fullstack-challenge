<template>
	<div :class="configuracaoPagina.nomeEstilo + 'Criar'">
		<div class="vx-card p-6">
			<vs-alert
				:active="(mensagemErro && mensagemErro != '' ? true : false)"
				color="danger"
				class="mb-10"
			>
				<span v-html="mensagemErro"></span>
			</vs-alert>
			<form @submit.prevent>
				<div class="vx-row mb-3">
					<div class="vx-col w-full">
						<vs-input
							name="nom"
							label="Nome"
							v-model="camposCriar.nome"
							class="w-full campoRequerido"
							v-validate="'required|min:3|max:100'"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Nome"
							:danger="errors.has('nome')"
							:success="!errors.has('email') && camposCriar.email != ''"
							:color="!errors.has('email') ? 'success' : 'danger'"
							:disabled="backendOcupado"
							ref="primeiroCampo"
						/>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('email')"
							:class="[
                                    {'block' : errors.has('email')}
                                ]"
						>{{ errors.first('email') }}</span>
					</div>
				</div>
				<div class="vx-row mb-3">
					<div class="vx-col w-full">
						<div
							class="vs-component vs-con-input-label vs-input w-full"
							:class="[
								{'vs-input-success' : !errors.has('preco')},
								{'vs-input-danger' : errors.has('preco')},
							]"
						>
							<label
								for="preco"
								class="vs-select--label campoRequerido"
								:class="[
									{'input-select-label-success--active' : !errors.has('preco') && camposCriar.preco && camposCriar.preco != '' && campoPrecoFocado},
									{'input-select-label-danger--active' : errors.has('preco')}
								]"
							>Preço</label>
							<div class="vs-con-input">
								<vue-numeric
									name="preco"
									currency=""
									separator="."
									decimal-separator=","
									thousand-separator="."
									v-validate="'required|decimal:2,.|min_value:0.01'"
									data-vv-as="Preço"
									:precision="2"
									:min="0.01"
									:empty-value="0.00"
									:minus="false"
									placeholder="0,00"
									output-type="Number"
									v-model="camposCriar.preco"
									class="vs-inputx vs-input--input normal w-full"
									:disabled="backendOcupado"
									@blur="campoPrecoFocado = false"
									@focus="campoPrecoFocado = true"
								></vue-numeric>
							</div>
						</div>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('preco')"
							:class="[
                                    {'block' : errors.has('preco')}
                                ]"
						>{{ errors.first('preco') }}</span>
					</div>
				</div>
				<div class="vx-row mb-3">
					<div class="vx-col w-1/2">
						<label
							for="id_categoria"
							class="vs-select--label campoRequerido"
							:class="[
								{'input-select-label-success--active' : !errors.has('id_categoria') && camposCriar.idCategoria && camposCriar.idCategoria != '' && campoIdCategoriaFocado},
								{'input-select-label-danger--active' : errors.has('id_categoria')}
							]"
						>Categoria</label>
						<v-select
							name="id_categoria"
							label="nome"
							placeholder="Categoria"
							v-model="camposCriar.idCategoria"
							class="w-full listagemCategoria"
							v-validate="{ required: true, included: listaCategorias.map(s => s.id) }"
							data-vv-as="Categoria"
							:filterable="true"
							:searchable="true"
							:reduce="item => item.id"
							:options="listaCategorias"
							:disabled="backendOcupado"
							:loading="obtendoListas"
							@search:focus="campoIdCategoriaFocado = true"
							@search:blur="campoIdCategoriaFocado = false"
						>
							<div slot="no-options">Nenhum registro disponível.</div>
						</v-select>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('id_categoria')"
							:class="[
								{'block' : errors.has('id_categoria')}
							]"
						>{{ errors.first('id_categoria') }}</span>
					</div>
					<div class="vx-col w-1/2">
						<label
							for="id_subcategoria"
							class="vs-select--label campoRequerido"
							:class="[
								{'input-select-label-success--active' : !errors.has('id_subcategoria') && camposCriar.idSubCategoria && camposCriar.idSubCategoria != '' && campoIdSubCategoriaFocado},
								{'input-select-label-danger--active' : errors.has('id_subcategoria')}
							]"
						>SubCategoria</label>
						<v-select
							name="id_subcategoria"
							label="nome"
							placeholder="SubCategoria"
							v-model="camposCriar.idSubCategoria"
							class="w-full listagemCategoria"
							v-validate="{ required: true, included: listaSubCategorias.map(s => s.id) }"
							data-vv-as="SubCategoria"
							:filterable="true"
							:searchable="true"
							:reduce="item => item.id"
							:options="listaSubCategorias"
							:disabled="backendOcupado"
							:loading="obtendoListas"
							@search:focus="campoIdSubCategoriaFocado = true"
							@search:blur="campoIdSubCategoriaFocado = false"
						>
							<div slot="no-options">Nenhum registro disponível.</div>
						</v-select>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('id_subcategoria')"
							:class="[
								{'block' : errors.has('id_subcategoria')}
							]"
						>{{ errors.first('id_subcategoria') }}</span>
					</div>
				</div>
				<div class="vx-row mb-3">
					<div class="vx-col w-full">
						<label
							for="imagem"
							class="vs-select--label"
						>Imagem</label>
						<vs-upload
							name="imagem"
							limit="1"
							action=""
							ref="imagemFoto"
							text="Selecionar arquivo"
							:disabled="backendOcupado"
							:show-upload-button="false"
							@update:vsFile="onImagemModificada"
							@on-delete="onImagemRemovida"
						/>
					</div>
				</div>
				<div class="vx-row mb-3 flex linhaCor">
					<autoextra
						:collection="camposCriar.cor"
						v-slot="{item,last,index}"
					>
						<template v-if="!last">
							<div class="vx-col ml-5">
								<label
									:for="'cor_' + index + '_cor'"
									class="vs-select--label input-select-label-success--active"
								>Cor {{(index+1)}}</label>
								<vx-input-group>
									<chrome-picker
										v-model="item.cor"
									/>
									<template slot="append">
										<div class="btn-addon ml-2">
											<vs-button
												:disabled="backendOcupado"
												@click="removerCor(index)"
												color="danger"
												type="filled"
												icon-pack="feather"
												icon="icon-trash"
											></vs-button>
										</div>
									</template>
								</vx-input-group>
							</div>
						</template>
						<template v-else>
						</template>
					</autoextra>
				</div>
				<div class="vx-row">
					<div class="vx-col w-full">
						<vs-button
							color="primary"
							type="filled"
							icon-pack="feather"
							icon="icon-plus"
							class="float-right"
							:disabled="backendOcupado"
							@click="criarCor"
						>Adicionar Nova Cor</vs-button>
					</div>
				</div>
				<div class="vx-row mb-3 mt-10">
					<div class="vx-col w-full sm:w-1/2">
						<vs-button
							color="warning"
							type="filled"
							:disabled="backendOcupado"
							@click="cancelar"
						>Cancelar</vs-button>
					</div>
					<div class="vx-col w-full sm:w-1/2">
						<vs-button
							color="primary"
							type="filled"
							class="float-right"
							:disabled="backendOcupado || formularioCriarInvalido"
							@click="processarCriar"
						>Criar {{ backendModel }}</vs-button>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>
import axios from "@/axios.js";
import vSelect from "vue-select";
import VueNumeric from "vue-numeric";
import Autoextra from "vue-autoextra";
import { Chrome } from "vue-color";

export default {
	$_veeValidate: {
		validator: "new",
	},
	components: {
		"v-select": vSelect,
		"chrome-picker": Chrome,
		VueNumeric,
		Autoextra
	},
	props: {
		abaAtiva: {
			type: Number,
			default: null,
		},
	},
	data() {
		return {
			configuracaoPagina: require("./configs.js"),
			backendOcupado: true,
			obtendoListas: false,
			listaCategorias: [],
			listaSubCategorias: [],
			campoPrecoFocado: false,
			campoIdCategoriaFocado: false,
			campoIdSubCategoriaFocado: false,
			mensagemErro: "",
			camposCriar: {
				imagemInserida: false,
				idCategoria: null,
				idSubCategoria: null,
				nome: "",
				preco: "",
				cor: []
			},
		};
	},
	computed: {
		backendUrl() {
			return this.$urlBasePath + this.configuracaoPagina.backendUrl;
		},
		backendModel() {
			return this.configuracaoPagina.backendModel;
		},
		formularioCriarInvalido() {
			var invalid = this.errors.any();
			if (!invalid && this.fields) {
				invalid = Object.keys(this.fields).some((key) => {
					if (this.fields[key].invalid) {
						return true;
					} else if (!this.fields[key].valid) {
						return true;
					}
				});
			}
			return invalid;
		},
		camposFormularioCriar() {
			return {
				id_categoria: this.camposCriar.idCategoria,
				id_subcategoria: this.camposCriar.idSubCategoria,
				nome: this.camposCriar.nome,
				preco: this.camposCriar.preco,
			};
		},
	},
	watch: {
		"camposCriar.idCategoria"(newVal, oldVal) {
			if (! newVal) {
				this.camposCriar.idSubCategoria = null;
				this.listaSubCategorias = [];
				this.$nextTick(() => {
					setTimeout(() => {
						this.errors.remove('id_subcategoria');
					}, 250);
				});
			} else if (newVal != oldVal &&
				this.camposCriar.idCategoria &&
				this.camposCriar.idCategoria != oldVal &&
				! this.backendOcupado
			) {
				this.backendOcupado = true;
				this.obtendoListas = true;
				this.camposCriar.idSubCategoria = null;
				this.listaSubCategorias = [];
				axios
					.get(this.backendUrl + `/subcategorias/${this.camposCriar.idCategoria}`)
					.then(response => {
						if (
							response &&
							response.status == 200 &&
							response.data &&
							response.data instanceof Object
						) {
							if (
								Object.prototype.hasOwnProperty.call(response.data, "subcategorias") &&
								response.data.subcategorias &&
								response.data.subcategorias instanceof Array
							) {
								this.listaSubCategorias = response.data.subcategorias;
							} else {
								this.$vs.notify({
									title: "Erro",
									text: "Incapaz de carregar a lista de subcategorias.",
									iconPack: "feather",
									icon: "icon-alert-circle",
									color: "danger",
								});
							}
						} else {
							this.$vs.notify({
								title: "Erro",
								text: "Incapaz de carregar a lista de subcategorias.",
								iconPack: "feather",
								icon: "icon-alert-circle",
								color: "danger",
							});
						}
						this.obtendoListas = false;
						this.backendOcupado = false;
						this.$nextTick(() => {
							setTimeout(() => {
								this.errors.remove('id_subcategoria');
							}, 250);
						});
					})
					.catch(error => {
						var errMsg = "Erro na requisição.";
						if (
							error &&
							error.response &&
							error.response.data &&
							error.response.data.message
						) {
							errMsg = error.response.data.message;
						} else if (
							error &&
							error.response &&
							error.response.data &&
							error.response.data.error
						) {
							errMsg = error.response.data.error;
						} else if (error && error.message) {
							errMsg = error.message;
						}
						if (
							error &&
							error.response &&
							error.response.status == 401
						) {
							this.$vs.notify({
								title: "Erro",
								text: errMsg,
								iconPack: "feather",
								icon: "icon-alert-circle",
								color: "danger",
								fixed: true,
							});
							setTimeout(() => {
								document.location.reload();
							}, 1500);
							return;
						}
						this.$vs.notify({
							title: "Erro",
							text: errMsg,
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
						});
						this.obtendoListas = false;
						this.backendOcupado = false;
						this.$nextTick(() => {
							setTimeout(() => {
								this.errors.remove('id_subcategoria');
							}, 250);
						});
					});
			}
		}
	},
	mounted() {
		this.backendOcupado = false;
		if (!this.$acl.check("modificar")) {
			this.cancelar();
		} else {
			this.backendOcupado = true;
			this.obtendoListas = true;
			axios
				.get(this.backendUrl + "/listas")
				.then(response => {
					if (
						response &&
						response.status == 200 &&
						response.data &&
						response.data instanceof Object
					) {
						if (
							Object.prototype.hasOwnProperty.call(response.data, "categorias") &&
							response.data.categorias &&
							response.data.categorias instanceof Array
						) {
							this.listaCategorias = response.data.categorias;
						} else {
							this.$vs.notify({
								title: "Erro",
								text: "Incapaz de carregar a lista de categorias.",
								iconPack: "feather",
								icon: "icon-alert-circle",
								color: "danger",
							});
						}
					} else {
						this.$vs.notify({
							title: "Erro",
							text: "Incapaz de carregar a lista de opções.",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
						});
					}
					this.obtendoListas = false;
					this.backendOcupado = false;
					this.$nextTick(() => {
						setTimeout(() => {
							window
								.jQuery(this.$refs.primeiroCampo.$refs.vsinput)
								.trigger("focus");
						}, 150);
					});
				})
				.catch(error => {
					var errMsg = "Erro na requisição.";
					if (
						error &&
						error.response &&
						error.response.data &&
						error.response.data.message
					) {
						errMsg = error.response.data.message;
					} else if (
						error &&
						error.response &&
						error.response.data &&
						error.response.data.error
					) {
						errMsg = error.response.data.error;
					} else if (error && error.message) {
						errMsg = error.message;
					}
					if (
						error &&
						error.response &&
						error.response.status == 401
					) {
						this.$vs.notify({
							title: "Erro",
							text: errMsg,
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
							fixed: true,
						});
						setTimeout(() => {
							document.location.reload();
						}, 1500);
						return;
					}
					this.$vs.notify({
						title: "Erro",
						text: errMsg,
						iconPack: "feather",
						icon: "icon-alert-circle",
						color: "danger",
					});
					this.obtendoListas = false;
					this.backendOcupado = false;
				});
		}
	},
	methods: {
		onImagemModificada(val) {
			this.camposCriar.imagemInserida = true;
		},
		onImagemRemovida(val) {
			this.camposCriar.imagemInserida = false;
		},
		criarCor() {
			this.camposCriar.cor.push({
				cor: {
					"hsl":{"h":0,"s":1,"l":0.5,"a":1},
					"hex":"#FF0000",
					"hex8":"#FF0000FF",
					"rgba":{"r":255,"g":0,"b":0,"a":1},
					"hsv":{"h":0,"s":1,"v":1,"a":1},
					"oldHue":0,
					"source":"hsva",
					"a":1
				}
			});
		},
		removerCor(index) {
			if (
				this.backendOcupado ||
				!this.camposCriar.cor ||
				!this.camposCriar.cor.length ||
				!this.camposCriar.cor[index]
			) {
				return;
			}
			this.backendOcupado = true;
			this.camposCriar.cor.splice(index, 1);
			this.backendOcupado = false;
		},
		cancelar() {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push(this.configuracaoPagina.routerPath);
		},
		processarCriar() {
			if (this.backendOcupado) {
				return;
			}
			this.mensagemErro = "";
			this.backendOcupado = true;
			this.$validator.validate().then(result => {
				if (result) {
					var formData = new FormData();
					for (var key in this.camposFormularioCriar) {
						formData.append(key, this.camposFormularioCriar[key]);
					}
					if (this.camposCriar.cor) {
						this.camposCriar.cor.forEach(item => {
							formData.append('cor[]', JSON.stringify(item.cor));
						});
					}
					if (
						this.camposCriar.imagemInserida &&
						this.$refs.imagemFoto.filesx &&
						this.$refs.imagemFoto.filesx.length > 0 &&
						this.$refs.imagemFoto.filesx[
							this.$refs.imagemFoto.filesx.length - 1
						] &&
						(!this.$refs.imagemFoto.filesx[
							this.$refs.imagemFoto.filesx.length - 1
						].hasOwnProperty("remove") ||
							!this.$refs.imagemFoto.filesx[
								this.$refs.imagemFoto.filesx.length - 1
							].remove)
					) {
						formData.append(
							"imagem",
							this.$refs.imagemFoto.filesx[
								this.$refs.imagemFoto.filesx.length - 1
							]
						);
					}
					axios
						.post(this.backendUrl, formData, {
							headers: {
								"Content-Type": "multipart/form-data",
							},
						})
						.then(response => {
							if (
								response &&
								response.status == 201 &&
								response.data &&
								response.data.status == "criado"
							) {
								this.$vs.notify({
									title: "Sucesso",
									text: `${this.backendModel} criado.`,
									iconPack: "feather",
									icon: "icon-check",
									color: "success",
								});
								setTimeout(() => {
									this.$router.push(
										this.configuracaoPagina.routerPath
									);
								}, 1000);
							} else {
								this.$vs.notify({
									title: "Erro",
									text:
										"Dados de resposta do registro criado inválidos.",
									iconPack: "feather",
									icon: "icon-alert-circle",
									color: "danger",
								});
								this.backendOcupado = false;
							}
						})
						.catch(error => {
							var errMsg = "Erro na requisição.";
							if (
								error &&
								error.response &&
								error.response.data &&
								error.response.data.message
							) {
								errMsg = error.response.data.message;
							} else if (
								error &&
								error.response &&
								error.response.data &&
								error.response.data.error
							) {
								errMsg = error.response.data.error;
							} else if (error && error.message) {
								errMsg = error.message;
							}
							if (
								error &&
								error.response &&
								error.response.status == 401
							) {
								this.$vs.notify({
									title: "Erro",
									text: errMsg,
									iconPack: "feather",
									icon: "icon-alert-circle",
									color: "danger",
									fixed: true,
								});
								setTimeout(() => {
									document.location.reload();
								}, 1500);
								return;
							}
							this.$vs.notify({
								title: "Erro",
								text: errMsg,
								iconPack: "feather",
								icon: "icon-alert-circle",
								color: "danger",
							});
							if (
								error &&
								error.response &&
								error.response.data &&
								error.response.data.errors
							) {
								if (
									error.response.data.errors instanceof
										Array ||
									error.response.data.errors instanceof Object
								) {
									var erros = {};
									Object.keys(
										this.camposFormularioCriar
									).forEach(key => {
										if (
											Object.prototype.hasOwnProperty.call(error.response.data.errors, key) &&
											error.response.data.errors[key] &&
											error.response.data.errors[key]
												.length > 0 &&
											error.response.data.errors[key][0]
										) {
											erros[key] = true;
											this.errors.add({
												field: key,
												msg:
													error.response.data.errors[
														key
													][0],
											});
										}
									});
									Object.keys(
										error.response.data.errors
									).forEach(key => {
										if (
											error.response.data.errors[key] &&
											error.response.data.errors[key]
												.length > 0 &&
											error.response.data.errors[
												key
											][0] &&
											(!erros ||
												! Object.prototype.hasOwnProperty.call(erros, key) ||
												!erros[key])
										) {
											erros[key] = true;
											this.mensagemErro +=
												error.response.data.errors[
													key
												][0].toString() + "<br/>";
										}
									});
								} else {
									this.mensagemErro = error.response.data.errors.toString();
								}
							}
							this.backendOcupado = false;
						});
				} else {
					this.backendOcupado = false;
				}
			});
		},
	},
};
</script>
<style lang="scss">
@import "./configs.scss";

.pagina#{$nomePagina}Criar {
	div.vs-input.campoRequerido > label,
	label.campoRequerido {
		font-weight: 500;
	}
	div.vs-input.campoRequerido > label::after,
	label.campoRequerido::after {
		content: " *";
		color: red;
	}
	.vs-select--label:not(.input-select-label-danger--active):not(.input-select-label-success--active) {
		color: rgba(0, 0, 0, 0.7);
	}
	div.listagemCategoria ul.vs__dropdown-menu {
		max-height: 200px;
	}
	div.widget-color-picker {
		width: 43px;
		height: 37px;
		cursor: pointer;
	}
	div.linhaCor.vx-input-group div.vx-input-group-default {
		max-width: 27%;
	}
	div.linhaCor > div {
		display: -webkit-box !important;
		display: flex !important;
		flex-wrap: wrap;
	}
	div.linhaCor > div > div  {
		-webkit-box-flex: 1 !important;
		flex: 1 1 0% !important;
		margin-bottom: 13px;
	}
	div.linhaCor div.vx-input-group-default.flex-grow {
		-webkit-box-flex: initial !important;
		flex-grow: initial !important;
	}
	div.con-upload div.con-img-upload {
		padding: 0px;
	}
	div.con-upload div.con-img-upload div.con-input-upload {
		margin: 0px;
	}
	div.con-upload div.con-img-upload div.con-input-upload span.text-input {
		font-weight: normal !important;
	}
}
</style>

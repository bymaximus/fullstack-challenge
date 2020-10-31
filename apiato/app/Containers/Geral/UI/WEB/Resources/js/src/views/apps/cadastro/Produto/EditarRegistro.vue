<template>
	<div :class="configuracaoPagina.nomeEstilo + 'Editar'">
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
							name="nome"
							label="Nome"
							v-model="camposEditar.nome"
							class="w-full campoRequerido"
							v-validate="'required|min:3|max:100'"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Nome"
							:danger="errors.has('nome')"
							:success="!errors.has('nome') && camposEditar.nome != ''"
							:color="!errors.has('nome') ? 'success' : 'danger'"
							:disabled="backendOcupado"
							ref="primeiroCampo"
						/>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('nome')"
							:class="[
                                    {'block' : errors.has('nome')}
                                ]"
						>{{ errors.first('nome') }}</span>
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
									{'input-select-label-success--active' : !errors.has('preco') && camposEditar.preco && camposEditar.preco != '' && campoPrecoFocado},
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
									v-model="camposEditar.preco"
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
								{'input-select-label-success--active' : !errors.has('id_categoria') && camposEditar.idCategoria && camposEditar.idCategoria != '' && campoIdCategoriaFocado},
								{'input-select-label-danger--active' : errors.has('id_categoria')}
							]"
						>Categoria</label>
						<v-select
							name="id_categoria"
							label="nome"
							placeholder="Categoria"
							v-model="camposEditar.idCategoria"
							class="w-full listagemCategoria"
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
								{'input-select-label-success--active' : !errors.has('id_subcategoria') && camposEditar.idSubCategoria && camposEditar.idSubCategoria != '' && campoIdSubCategoriaFocado},
								{'input-select-label-danger--active' : errors.has('id_subcategoria')}
							]"
						>SubCategoria</label>
						<v-select
							name="id_subcategoria"
							label="nome"
							placeholder="SubCategoria"
							v-model="camposEditar.idSubCategoria"
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
				<div
					class="vx-row mb-3"
					v-show="camposEditar.imagem != null && camposEditar.imagem != '' && camposEditar.imagem.toString().startsWith('data:image/') && /^data:image\/.*;base64,/.test(camposEditar.imagem)"
				>
					<div
						class="vx-col imagemFoto"
						@mouseover="imagemHover = true"
						@mouseleave="imagemHover = false"
					>
						<img v-bind:src="camposEditar.imagem" />
						<vs-button
							v-if="imagemHover"
							color="danger"
							type="filled"
							class="float-right"
							:disabled="backendOcupado || ! imagemHover"
							@click="removerImagem"
						>Remover</vs-button>
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
						:collection="camposEditar.cor"
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
							:disabled="backendOcupado || formularioEditarInvalido"
							@click="processarEditar"
						>Salvar {{ backendModel }}</vs-button>
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
			imagemHover: false,
			listaCategorias: [],
			listaSubCategorias: [],
			campoPrecoFocado: false,
			campoIdCategoriaFocado: false,
			campoIdSubCategoriaFocado: false,
			mensagemErro: "",
			camposEditar: {
				imagemInserida: false,
				imagemRemovida: false,
				imagem: null,
				idCategoria: null,
				idSubCategoria: null,
				nome: "",
				preco: "",
				cor: [],
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
		formularioEditarInvalido() {
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
		camposFormularioEditar() {
			return {
				id_categoria: this.camposEditar.idCategoria,
				id_subcategoria: this.camposEditar.idSubCategoria,
				nome: this.camposEditar.nome,
				preco: this.camposEditar.preco,
			};
		},
	},
	watch: {
		"camposEditar.idCategoria"(newVal, oldVal) {
			if (! newVal) {
				this.camposEditar.idSubCategoria = null;
				this.listaSubCategorias = [];
				this.$nextTick(() => {
					setTimeout(() => {
						this.errors.remove('id_subcategoria');
					}, 250);
				});
			} else if (newVal != oldVal &&
				this.camposEditar.idCategoria &&
				this.camposEditar.idCategoria != oldVal &&
				! this.backendOcupado
			) {
				this.backendOcupado = true;
				this.obtendoListas = true;
				this.camposEditar.idSubCategoria = null;
				this.listaSubCategorias = [];
				axios
					.get(this.backendUrl + `/subcategorias/${this.camposEditar.idCategoria}`)
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
	methods: {
		onImagemModificada(val) {
			this.camposEditar.imagemInserida = true;
		},
		onImagemRemovida(val) {
			this.camposEditar.imagemInserida = false;
		},
		removerImagem() {
			this.camposEditar.imagemRemovida = true;
			this.camposEditar.imagem = null;
			this.imagemHover = false;
		},
		criarCor(cor) {
			if (! cor ||
				typeof cor == 'undefined'
			) {
				cor = {
					"hsl":{"h":0,"s":1,"l":0.5,"a":1},
					"hex":"#FF0000",
					"hex8":"#FF0000FF",
					"rgba":{"r":255,"g":0,"b":0,"a":1},
					"hsv":{"h":0,"s":1,"v":1,"a":1},
					"oldHue":0,
					"source":"hsva",
					"a":1
				};
			}
			this.camposEditar.cor.push({
				cor: cor
			});
		},
		removerCor(index) {
			if (
				this.backendOcupado ||
				!this.camposEditar.cor ||
				!this.camposEditar.cor.length ||
				!this.camposEditar.cor[index]
			) {
				return;
			}
			this.backendOcupado = true;
			this.camposEditar.cor.splice(index, 1);
			this.backendOcupado = false;
		},
		cancelar() {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push(this.configuracaoPagina.routerPath);
		},
		carregarRegistro(registro) {
			this.camposEditar.idCategoria = registro.id_categoria;
			this.camposEditar.idSubCategoria = registro.id_subcategoria;
			this.camposEditar.nome = registro.nome;
			this.camposEditar.preco = registro.preco;
			this.camposEditar.imagem = registro.imagem;
			this.camposEditar.cor = [];
			if (registro.cor) {
				registro.cor.forEach(cor => {
					this.criarCor(cor);
				});
			}
		},
		processarEditar() {
			if (this.backendOcupado) {
				return;
			}
			this.mensagemErro = "";
			this.backendOcupado = true;
			this.$validator.validate().then(result => {
				if (result) {
					var formData = new FormData();
					for (var key in this.camposFormularioEditar) {
						formData.append(key, this.camposFormularioEditar[key]);
					}
					if (this.camposEditar.cor) {
						this.camposEditar.cor.forEach(item => {
							formData.append('cor[]', JSON.stringify(item.cor));
						});
					}
					if (
						this.camposEditar.imagemInserida &&
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
					} else if (this.camposEditar.imagemRemovida) {
						formData.append("imagem_removida", "1");
					}
					formData.append("_method", "PATCH");
					axios
						.post(
							this.backendUrl + `/${this.$route.params.id}`,
							formData,
							{
								headers: {
									"Content-Type": "multipart/form-data",
								},
							}
						)
						.then(response => {
							if (
								response &&
								response.status == 200 &&
								response.data &&
								response.data.status == "atualizado"
							) {
								this.$vs.notify({
									title: "Sucesso",
									text: `${this.backendModel} atualizado.`,
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
										"Dados de resposta do registro atualizado inválidos.",
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
										this.camposFormularioEditar
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
												!Object.prototype.hasOwnProperty.call(erros, key) ||
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
	mounted() {
		if (!this.$acl.check("modificar")) {
			this.backendOcupado = false;
			this.cancelar();
		} else {
			this.backendOcupado = true;
			this.obtendoListas = true;
			axios
				.get(this.backendUrl + `/${this.$route.params.id}`)
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
							if (
								Object.prototype.hasOwnProperty.call(response.data, "registro") &&
								response.data.registro &&
								response.data.registro instanceof Object
							) {
								this.carregarRegistro(
									Object.assign({}, response.data.registro)
								);
							} else {
								this.$vs.notify({
									title: "Erro",
									text:
										"Incapaz de carregar o " +
										this.backendModel.toLowerCase() +
										".",
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
					} else {
						this.$vs.notify({
							title: "Erro",
							text:
								"Incapaz de carregar o " +
								this.backendModel.toLowerCase() +
								".",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
						});
					}
					this.$nextTick(() => {
						this.backendOcupado = false;
						this.obtendoListas = false;
						this.$refs.primeiroCampo.$el.focus();
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
					this.backendOcupado = false;
					this.obtendoListas = false;
				});
		}
	},
};
</script>
<style lang="scss">
@import "./configs.scss";

.pagina#{$nomePagina}Editar {
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
	.imagemFoto {
		position: relative;
	}
	.imagemFoto img {
		max-width: 450px;
	}
	.imagemFoto button {
		width: 120px;
		position: absolute;
		top: 50%;
		left: calc(50% - 60px);
	}
}
</style>

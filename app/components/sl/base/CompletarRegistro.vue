<template>
  <div>
    <div v-if="!response.replied">
      <div class="field">
        <label class="label">Nombre *</label>
        <div class="control">
          <input
            type="text"
            v-model="user.names"
            name="names"
            v-validate="'required|alpha_spaces|min:2|max:25'"
            class="input has-text-centered is-medium"
            placeholder="Ingresá su nombre(s)"
          />
          <span class="help is-danger" v-show="errors.has('names')">
            <i class="fas fa-times-circle fa-fw"></i> Error. Campo requerido y hasta 25 caracteres
          </span>
        </div>
      </div>
      <div class="field">
        <label class="label">Apellido *</label>
        <div class="control">
          <input
            type="text"
            v-model="user.surnames"
            name="surnames"
            v-validate="'required|alpha_spaces|min:2|max:25'"
            class="input has-text-centered is-medium"
            placeholder="Ingrese su apellido(s)"
          />
          <span class="help is-danger" v-show="errors.has('surnames')">
            <i class="fas fa-times-circle fa-fw"></i> Error. Campo requerido y hasta 25 caracteres
          </span>
        </div>
      </div>
      <div class="field">
        <label class="label">Fecha de nacimiento</label>
        <div class="control">
          <b-datepicker
            placeholder="Hace clic para seleccionar la fecha"
            v-model="inputBirthday"
            :mobile-native="false"
            size="is-medium"
            :date-formatter="(date) => date.toLocaleDateString('es-AR')"
            :max-date="new Date()"
            icon="calendar-alt"
          ></b-datepicker>
          <span v-show="errors.has('inputBirthday')" class="help is-danger">
            <i class="fas fa-times-circle fa-fw"></i>
            &nbsp;{{errors.first('inputBirthday')}}
          </span>
          <input
            type="hidden"
            v-model="inputBirthday"
            v-validate="'required'"
            data-vv-name="inputBirthday"
            data-vv-as="'Fecha de nacimiento'"
          />
        </div>
      </div>
      <div class="field">
        <label class="label">Nacionalidad (Pais de origen)</label>
        <div class="control">
          <b-autocomplete
            v-model="inputNationality"
            :data="filteredNationalities"
            placeholder="Escriba el pais de su nacionalidad"
            size="is-medium"
            @select="option => user.nationality = option"
          >
            <template slot="empty">Sin resultados</template>
          </b-autocomplete>
        </div>
      </div>
      <div class="field">
        <label class="label">Ingrese su DNI</label>
        <div class="control">
          <input
            type="text"
            v-model="user.dni"
            name="dni"
            v-validate="'required|numeric'"
            ref="dni"
            class="input has-text-centered is-medium"
            placeholder="Ingrese su DNI"
          />
          <span class="help is-danger" v-show="errors.has('dni')">
            <i class="fas fa-times-circle fa-fw"></i> Error. El DNI no puede ser vacio
          </span>
        </div>
      </div>
      <div class="field">
        <label class="label">Confirme su DNI</label>
        <div class="control">
          <input
            type="text"
            v-model.lazy="repeatDNI"
            name="re-dni"
            v-validate="'required|numeric|confirmed:dni'"
            class="input has-text-centered is-medium"
            placeholder="Vuelva a ingresar su DNI"
          />
          <span class="help is-danger" v-show="errors.has('re-dni')">
            <i class="fas fa-times-circle fa-fw"></i> Error. El DNI no coincide
          </span>
        </div>
      </div>
      <br />
      <div class="message is-primary">
        <div class="message-body">
          <div class="field has-text-left">
            <label class="label">¿A que barrio pertenece?</label>
            <!-- <label class="label is-pulled-right">
                <a href="#">No lo sé
                  <i class="far fa-question-circle fa-lg fa-fw"></i>
                </a>
            </label>-->

            <div class="control">
              <div class="select is-medium is-fullwidth">
                <select name="district" v-model="user.district_id" v-validate="'required'">
                  <option :value="null">Seleccione su barrio</option>
                  <option
                    v-for="district in districts"
                    :key="district.id"
                    :value="district.id"
                  >{{district.name}}</option>
                </select>
              </div>
              <span class="help is-danger" v-show="errors.has('district')">
                <i class="fas fa-times-circle fa-fw"></i> Debe seleccionar el barrio en donde vive.
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="field">
        <label class="label">Contraseña *</label>
        <div class="control">
          <input
            type="password"
            v-model="user.password"
            name="password"
            ref="password"
            v-validate="'required|min:6'"
            class="input has-text-centered is-medium"
            placeholder="Ingrese tu contraseña"
          />
          <span class="help is-danger" v-show="errors.has('password')">
            <i class="fas fa-times-circle fa-fw"></i> Error. La contraseña no puede ser vacia, (Mínimo 6 caracteres)
          </span>
        </div>
      </div>
      <div class="field">
        <label class="label">Reescriba su contraseña *</label>
        <div class="control">
          <input
            type="password"
            v-model="repeatPassword"
            name="re-password"
            v-validate="'required|confirmed:password'"
            class="input has-text-centered is-medium"
            placeholder="Vuelva a ingresar la contraseña"
          />
          <span class="help is-danger" v-show="errors.has('re-password')">
            <i class="fas fa-times-circle fa-fw"></i> Error. La contraseña no coincide
          </span>
        </div>
      </div>
      <hr />
      <div class="field">
        <div class="control">
          <button
            @click="submitSignUp"
            :disabled="errors.count() > 0 || repeatPassword == ''"
            class="button is-large is-primary is-rounded is-fullwidth"
            :class="{'is-loading': isLoading}"
          >
            <i class="fas fa-user-plus"></i>&nbsp;&nbsp;Crear cuenta
          </button>
        </div>
      </div>
    </div>
    <div v-else>
      <div v-show="response.replied && response.ok">
        <div class="notification is-success">
          <i class="fas fa-check fa-lg fa-fw"></i>
          ¡Tu cuenta ha sido creada correctamente y ya podés comenzar a participar del Presupuesto Participativo de San Martín!
        </div>
        <a :href="logInUrl" class="button is-primary is-rounded is-medium is-fullwidth">
          <i class="fas fa-sign-in-alt fa-lg fa-fw"></i>&nbsp;Iniciar sesión
        </a>
      </div>
      <div v-show="response.replied && !response.ok">
        <div class="notification is-danger">
          <i class="fas fa-times fa-lg fa-fw"></i>
          Ha ocurrido un error al crear su cuenta.
          <span
            v-if="response.message"
            class="is-size-7"
          >
            <br />
            {{response.message}}
          </span>
          <br />Por favor intente más tarde
        </div>
        <a @click="reload" class="button is-dark is-medium is-fullwidth is-300">
          <i class="fas fa-sync-alt fa-lg fa-fw"></i>&nbsp;Volver a intentar
        </a>
        <a href="/" class="button is-white is-medium is-fullwidth is-300">
          <i class="fas fa-home fa-lg fa-fw"></i>&nbsp;Ir al inicio
        </a>
      </div>
    </div>
    <b-loading :is-full-page="false" :active.sync="isLoading"></b-loading>
  </div>
</template>

<script>
export default {
  props: ["formUrl", "logInUrl", "token", "districts"],
  data() {
    return {
      user: {
        names: "",
        surnames: "",
        password: null,
        dni: "",
        district_id: null,
        token: this.token,
        birthday: null,
        nationality: null
      },
      inputBirthday: null,
      inputNationality: "",
      repeatPassword: "",
      repeatDNI: "",
      isLoading: false,
      response: {
        replied: null,
        ok: null,
        message: null
      },
      nationalities: [
        "Afganistán",
        "Albania",
        "Argelia",
        "Samoa Americana",
        "Andorra",
        "Angola",
        "Anguila",
        "Antártida",
        "Antigua y Barbuda",
        "Argentina",
        "Armenia",
        "Aruba",
        "Australia",
        "Austria",
        "Azerbaiyán",
        "Bahamas",
        "Bahrein",
        "Bangladesh",
        "Barbados",
        "Belarús",
        "Bélgica",
        "Belice",
        "Benin",
        "Bermudas",
        "Bhután",
        "Bolivia",
        "Bosnia y Herzegovina",
        "Botswana",
        "Isla Bouvet",
        "Brasil",
        "Territorio Británico del Océano Índico",
        "Brunei Darussalam",
        "Bulgaria",
        "Burkina Faso",
        "Burundi",
        "Camboya",
        "Camerún",
        "Canadá",
        "Cabo Verde",
        "Islas Caimán",
        "República Centroafricana",
        "Chad",
        "Chile",
        "China",
        "Isla de Navidad",
        "Islas Cocos (Keeling)",
        "Colombia",
        "Comoras",
        "Congo",
        "Congo (República Democrática del)",
        "Islas Cook",
        "Costa Rica",
        "Costa de Marfil",
        "Croacia",
        "Cuba",
        "Chipre",
        "República Checa",
        "Dinamarca",
        "Djibouti",
        "Dominica",
        "República Dominicana",
        "Ecuador",
        "Egipto",
        "El Salvador",
        "Guinea Ecuatorial",
        "Eritrea",
        "Estonia",
        "Etiopía",
        "Islas Malvinas",
        "Islas Feroe",
        "Fiji",
        "Finlandia",
        "Francia",
        "Guayana Francesa",
        "Polinesia Francesa",
        "Tierras Australes Francesas",
        "Gabón",
        "Gambia",
        "Georgia",
        "Alemania",
        "Ghana",
        "Gibraltar",
        "Grecia",
        "Groenlandia",
        "Granada",
        "Guadeloupe",
        "Guam",
        "Guatemala",
        "Guinea",
        "Guinea Bissau",
        "Guyana",
        "Haití",
        "Heard e Islas McDonald",
        "Santa Sede",
        "Honduras",
        "Hong Kong",
        "Hungría",
        "Islandia",
        "India",
        "Indonesia",
        "Irán (República Islámica de)",
        "Iraq",
        "Irlanda",
        "Israel",
        "Italia",
        "Jamaica",
        "Japón",
        "Jordania",
        "Kazajstán",
        "Kenya",
        "Kiribati",
        "República Popular Democrática de Corea",
        "República de Corea",
        "Kuwait",
        "Kirguistán",
        "República Democrática Popular de Lao",
        "Letonia",
        "Líbano",
        "Lesotho",
        "Liberia",
        "Libia",
        "Liechtenstein",
        "Lituania",
        "Luxemburgo",
        "Macao",
        "Macedonia del Norte",
        "Madagascar",
        "Malawi",
        "Malasia",
        "Maldivas",
        "Malí",
        "Malta",
        "Islas Marshall",
        "Martinique",
        "Mauritania",
        "Mauricio",
        "Mayotte",
        "México",
        "Micronesia",
        "Moldavia",
        "Mónaco",
        "Mongolia",
        "Montserrat",
        "Marruecos",
        "Mozambique",
        "Myanmar",
        "Namibia",
        "Nauru",
        "Nepal",
        "Países Bajos",
        "Nueva Caledonia",
        "Nueva Zelanda",
        "Nicaragua",
        "Níger",
        "Nigeria",
        "Niue",
        "Isla Norfolk",
        "Isla Marianas del Norte",
        "Noruega",
        "Omán",
        "Pakistán",
        "Palau",
        "Palestina",
        "Panamá",
        "Papua Nueva Guinea",
        "Paraguay",
        "Perú",
        "Filipinas",
        "Pitcairn",
        "Polonia",
        "Portugal",
        "Puerto Rico",
        "Qatar",
        "Reunión",
        "Rumania",
        "Rusia",
        "Rwanda",
        "Santa Helena, Ascensión y Tristán de Acuña",
        "Saint Kitts y Nevis",
        "Santa Lucía",
        "San Pedro y Miquelón",
        "San Vicente y las Granadinas",
        "Samoa",
        "San Marino",
        "Santo Tomé y Príncipe",
        "Arabia Saudita",
        "Senegal",
        "Seychelles",
        "Sierra Leona",
        "Singapur",
        "Eslovaquia",
        "Eslovenia",
        "Islas Salomón",
        "Somalia",
        "Sudáfrica",
        "Georgia del Sur y las Islas Sandwich del Sur",
        "España",
        "Sri Lanka",
        "Sudan",
        "Suriname",
        "Svalbard y Jan Mayen",
        "Swazilandia",
        "Suecia",
        "Suiza",
        "República Árabe Siria",
        "Taiwán",
        "Tayikistán",
        "Tanzania",
        "Tailandia",
        "Timor-Leste",
        "Togo",
        "Tokelau",
        "Tonga",
        "Trinidad y Tobago",
        "Túnez",
        "Turquía",
        "Turkmenistán",
        "Islas Turcas y Caicos",
        "Tuvalu",
        "Uganda",
        "Ucrania",
        "Emiratos Árabes Unidos",
        "Reino Unido",
        "Estados Unidos",
        "Islas Ultramarinas Menores de los Estados Unidos",
        "Uruguay",
        "Uzbekistán",
        "Vanuatu",
        "Venezuela",
        "Vietnam",
        "Islas Vírgenes británicas",
        "Islas Vírgenes de los Estados Unidos",
        "Wallis y Futuna",
        "Sahara Occidental",
        "Yemen",
        "Zambia",
        "Zimbabwe",
        "Islas Åland",
        "Bonaire, San Eustaquio y Saba",
        "Curaçao",
        "Guernsey",
        "Isla de Man",
        "Jersey",
        "Montenegro",
        "Saint Barthélemy",
        "Saint Martin (francesa)",
        "Serbia",
        "Sint Maarten (neerlandesa)",
        "Sudán del Sur",
        "Kosovo"
      ]
    };
  },
  methods: {
    submitSignUp: function() {
      this.$validator
        .validateAll()
        .then(result => {
          if (!result) {
            this.$snackbar.open({
              message: "Error. Verifique los campos.",
              type: "is-danger",
              actionText: "Cerrar"
            });
            return false;
          }
          this.isLoading = true;
          this.$http
            .post(this.formUrl, this.user)
            .then(response => {
              window.gtag("event", "validationSuccess", {
                event_category: "Sign Up",
                event_label:
                  "New user (" + this.token + ") completed sign up process"
              });
              this.isLoading = false;
              this.response.replied = true;
              this.response.ok = true;
            })
            .catch(error => {
              // window.gtag("event", "validationFailed", {
              //   event_category: "Sign Up",
              //   event_label: "Failed to validate user (" + this.token + ")"
              // });
              console.log(error.response.data.message);
              this.isLoading = false;
              this.response.replied = true;
              this.response.message = error.response.data.message;
              this.response.ok = false;
              this.$snackbar.open({
                message: "Error inesperado",
                type: "is-danger",
                actionText: "Cerrar"
              });
              return false;
            });
        })
        .catch(error => {
          this.$snackbar.open({
            message: "Error inesperado",
            type: "is-danger",
            actionText: "Cerrar"
          });
          return false;
        });
    },
    reload: function() {
      window.location.reload();
    }
  },
  computed: {
    filteredNationalities() {
      return this.nationalities.filter(option => {
        return (
          option
            .toString()
            .toLowerCase()
            .indexOf(this.inputNationality.toLowerCase()) >= 0
        );
      });
    }
  },
  watch: {
    inputBirthday: function(newVal) {
      this.user.birthday = newVal.toISOString().split("T")[0];
    }
  }
};
</script>

<style lang="scss">
.datepicker .control input.input {
  text-align: center;
  padding-left: 0;
  padding-right: 0;
}
.autocomplete .control input.input {
  text-align: center;
}
</style>

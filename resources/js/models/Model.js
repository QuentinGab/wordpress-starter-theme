import { Model as BaseModel } from "vue-api-query";

export default class Model extends BaseModel {
  constructor(...attributes) {
    super(...attributes);

    this._castProperties();
  }

  // define a base url for a REST API
  baseURL() {
    return "/wp-json/your-namespace";
  }
  // implement a default request method
  request(config) {
    return this.$http.request(config);
  }

  is(model) {
    return (
      this.constructor.name.toLowerCase() ===
        model.constructor.name.toLowerCase() &&
      this.getPrimaryKey() === model.getPrimaryKey()
    );
  }

  /**
   * Casts field to their Relationship Model
   */
  relations() {
    return {
      created_at: Date,
      updated_at: Date,
    };
  }

  casts() {
    return {};
  }

  _castProperties() {
    Object.entries(this.casts()).forEach(([property, callback]) => {
      if (this.has(property)) {
        this[property] = callback(this[property]);
      }
    });
  }

  has(...properties) {
    return properties.every((property) => this.hasProperty(property));
  }

  hasProperty(prop) {
    const property = get(this, prop, null);

    if (property === null) {
      return false;
    }

    if (property === "undefined") {
      return false;
    }

    if (Array.isArray(property) && property.length === 0) {
      return false;
    }

    return true;
  }

  //fixes
  /**
   * Define if the model should hydrate data from PUT response
   * @returns bool
   */
  hydrateOnUpdate() {
    return true;
  }

  /**
   * Override
   * Auto hydrate on save
   */
  _create() {
    return super._create().then((model) => {
      Object.assign(this, model);
      return this;
    });
  }

  /**
   * Override
   * Auto hydrate on save
   */
  _update() {
    if (this.hydrateOnUpdate()) {
      return super._update().then((model) => {
        Object.assign(this, model);
        return this;
      });
    }

    return super._update();
  }

  for(...args) {
    if (!this._fromResource) {
      return super.for(...args);
    }
    return this;
  }
}

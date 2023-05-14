// noinspection NpmUsedModulesInstalled,JSFileReferences
import FormEngineValidation from '@typo3/backend/form-engine-validation.js';

export class FormEngineEvaluation {
  static registerCustomEvaluation(name) {
    FormEngineValidation.registerCustomEvaluation(name, FormEngineEvaluation.evaluateAspectRatio);
  }

  static evaluateAspectRatio(value) {
    if (!value) {
      return '';
    }

    value = value.trim();

    const matches = value.match(/(\d+):(\d+)/);
    if (!matches || matches.length !== 3) {
      return '';
    }

    const width = parseInt(matches[1], 10);
    const height = parseInt(matches[2], 10);

    return (width > 0 && height > 0) ? value : '';
  }
}

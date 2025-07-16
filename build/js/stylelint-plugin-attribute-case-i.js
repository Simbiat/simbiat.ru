// Plugin to force `i` flag for comparison of attribute values
const stylelint = require("stylelint");
const selectorParser = require("postcss-selector-parser");

const ruleName = "simbiat/require-attribute-i-flag";

const messages = stylelint.utils.ruleMessages(ruleName, {
  expected: attr => `Expected attribute selector [${attr}] to include the 'i' case-insensitive flag`,
});

const meta = {
  fixable: "code",
  url: "https://github.com/simbiat/stylelint-require-attribute-i-flag"
};

/** @type {import('stylelint').Rule} */
const ruleFunction = (primary, _secondaryOptions) => {
  return (root, result) => {
    const validOptions = stylelint.utils.validateOptions(result, ruleName, {
      actual: primary,
      possible: [true]
    });
    
    if (!validOptions) return;
    
    root.walkRules((ruleNode) => {
      const rawSelector = ruleNode.raws.selector?.raw || ruleNode.selector;
      
      selectorParser((selectors) => {
        selectors.walkAttributes((attr) => {
          const attrName = attr.attribute;
          const attrValue = attr.value?.replace(/^["']|["']$/g, "");
          const hasIFlag = attr.insensitive;
          
          if (hasIFlag || attrValue === undefined || attrValue === "") return;
          
          const fullAttr = attr.toString();
          const rawAttr = `${attrName}='${attrValue}'`;
          const index = rawSelector.indexOf(fullAttr);
          const endIndex = index + fullAttr.length;
          
          stylelint.utils.report({
            result,
            ruleName,
            node: ruleNode,
            message: messages.expected(rawAttr),
            index,
            endIndex,
            fix: (fixer) => {
              const fixed = rawSelector.replace(
                fullAttr,
                fullAttr.replace(/\]$/, " i]")
              );
              ruleNode.selector = fixed;
              return true;
            }
          });
        });
      }).processSync(rawSelector);
    });
  };
};

ruleFunction.ruleName = ruleName;
ruleFunction.messages = messages;
ruleFunction.meta = meta;

module.exports = stylelint.createPlugin(ruleName, ruleFunction);